-- SMART_GYM DB - MySQL/MariaDB compatible (utf8mb4_unicode_ci)
-- Crea todo desde cero (tablas, FKs, vistas y datos semilla)

-- 0) Base de datos
CREATE DATABASE IF NOT EXISTS smart_gym
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE smart_gym;

-- Limpieza opcional (descomenta si necesitas reiniciar)
-- SET FOREIGN_KEY_CHECKS=0;
-- DROP VIEW IF EXISTS vw_class_spots, vw_active_memberships;
-- DROP TABLE IF EXISTS posts, body_measurements, routine_items, routines,
--   payments, user_memberships, membership_plans, class_reservations,
--   classes, activity_types, users, gyms;
-- SET FOREIGN_KEY_CHECKS=1;

-- 1) Gimnasios
CREATE TABLE gyms (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  address VARCHAR(200),
  phone VARCHAR(30),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2) Usuarios
CREATE TABLE users (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  gym_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  age TINYINT UNSIGNED,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_gym
    FOREIGN KEY (gym_id) REFERENCES gyms(id)
      ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 3) Tipos de Actividad
CREATE TABLE activity_types (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL UNIQUE,
  description VARCHAR(255)
) ENGINE=InnoDB;

-- 4) Clases
CREATE TABLE classes (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  gym_id BIGINT UNSIGNED NOT NULL,
  activity_type_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(120) NOT NULL,
  start_at DATETIME NOT NULL,
  end_at   DATETIME NOT NULL,
  capacity SMALLINT UNSIGNED NOT NULL DEFAULT 20,
  trainer VARCHAR(120),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY ix_classes_start (start_at),
  KEY ix_classes_activity (activity_type_id),
  CONSTRAINT fk_classes_gym
    FOREIGN KEY (gym_id) REFERENCES gyms(id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_classes_activity
    FOREIGN KEY (activity_type_id) REFERENCES activity_types(id)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 5) Reservas de Clase
CREATE TABLE class_reservations (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  class_id BIGINT UNSIGNED NOT NULL,
  user_id  BIGINT UNSIGNED NOT NULL,
  status ENUM('PENDING','CONFIRMED','CANCELLED','ATTENDED')
         NOT NULL DEFAULT 'PENDING',
  comment VARCHAR(255),
  reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_class (class_id, user_id),
  CONSTRAINT fk_resv_class
    FOREIGN KEY (class_id) REFERENCES classes(id)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_resv_user
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6) Planes de Membresía (catálogo)
CREATE TABLE membership_plans (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL UNIQUE,
  duration_days SMALLINT UNSIGNED NOT NULL, -- 30,90,365
  price DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

-- 7) Membresías por Usuario (suscripción real)
CREATE TABLE user_memberships (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  plan_id BIGINT UNSIGNED NOT NULL,
  start_date DATE NOT NULL,
  end_date   DATE NOT NULL,
  status ENUM('ACTIVE','EXPIRED','CANCELLED') NOT NULL DEFAULT 'ACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY ix_um_user (user_id),
  KEY ix_um_plan (plan_id),
  KEY ix_um_status (status),
  CONSTRAINT fk_um_user
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_um_plan
    FOREIGN KEY (plan_id) REFERENCES membership_plans(id)
      ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 8) Pagos (vinculables a membresía o reserva)
CREATE TABLE payments (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  membership_id BIGINT UNSIGNED NULL,
  reservation_id BIGINT UNSIGNED NULL,
  method ENUM('CARD','CASH','TRANSFER') NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY ix_pay_user (user_id),
  KEY ix_pay_paid_at (paid_at),
  CONSTRAINT fk_pay_user
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_pay_membership
    FOREIGN KEY (membership_id) REFERENCES user_memberships(id)
      ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_pay_reservation
    FOREIGN KEY (reservation_id) REFERENCES class_reservations(id)
      ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 9) Rutinas
CREATE TABLE routines (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  type VARCHAR(60),   -- fuerza/cardio/mixto
  level ENUM('BEGINNER','INTERMEDIATE','ADVANCED') NOT NULL DEFAULT 'BEGINNER',
  goal  VARCHAR(160),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY ix_routines_user (user_id),
  CONSTRAINT fk_routines_user
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10) Ítems de rutina (Drag & Drop por sort_order)
CREATE TABLE routine_items (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  routine_id BIGINT UNSIGNED NOT NULL,
  activity_type_id BIGINT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL, -- sentadilla, press, etc.
  reps VARCHAR(40),           -- "4x10", "AMRAP 10m"
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  KEY ix_ri_routine (routine_id, sort_order),
  CONSTRAINT fk_ri_routine
    FOREIGN KEY (routine_id) REFERENCES routines(id)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_ri_activity
    FOREIGN KEY (activity_type_id) REFERENCES activity_types(id)
      ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 11) Mediciones corporales (IMC generado)
CREATE TABLE body_measurements (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  measured_at DATE NOT NULL,
  weight_kg DECIMAL(5,2),
  height_m  DECIMAL(3,2),
  body_fat_percent DECIMAL(4,1),
  -- IMC calculado (MariaDB no soporta columnas generadas STORED antiguas en todas las versiones;
  -- si da error, eliminar la línea siguiente y calcular en SELECT)
  bmi DECIMAL(5,2) GENERATED ALWAYS AS
      (CASE WHEN height_m IS NULL OR height_m=0 THEN NULL
            ELSE weight_kg/(height_m*height_m) END) VIRTUAL,
  UNIQUE KEY uq_bm_user_date (user_id, measured_at),
  CONSTRAINT fk_bm_user
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- 12) Noticias / Blog
CREATE TABLE posts (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NULL,     -- autor
  title VARCHAR(160) NOT NULL,
  body TEXT NOT NULL,
  published_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY ix_posts_pub (published_at),
  CONSTRAINT fk_posts_user
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- 13) Vistas útiles
DROP VIEW IF EXISTS vw_class_spots;
CREATE VIEW vw_class_spots AS
SELECT c.id AS class_id, c.title, c.capacity,
       c.capacity - COUNT(CASE WHEN r.status IN ('PENDING','CONFIRMED') THEN 1 END) AS spots_left
FROM classes c
LEFT JOIN class_reservations r ON r.class_id = c.id
GROUP BY c.id, c.title, c.capacity;

DROP VIEW IF EXISTS vw_active_memberships;
CREATE VIEW vw_active_memberships AS
SELECT um.*
FROM user_memberships um
WHERE um.status='ACTIVE'
  AND CURDATE() BETWEEN um.start_date AND um.end_date;

-- 14) Datos semilla
INSERT INTO gyms(name,address,phone)
VALUES ('Smart Gym Central','San José','+506 0000-0000');

INSERT INTO activity_types(name,description)
VALUES ('Yoga','Posturas y respiración'),
       ('CrossFit','Alta intensidad'),
       ('Ballet','Técnica y danza');

INSERT INTO membership_plans(name,duration_days,price)
VALUES ('Basic',30,29.00),('Pro',30,59.00),('Elite',30,99.00);