-- =========================================================
-- SMART_GYM - Esquema + Vistas + Datos semilla (MySQL/MariaDB)
-- Charset: utf8mb4_unicode_ci | Motor: InnoDB
-- Idempotente: seguro de re-ejecutar
-- =========================================================

-- 0) Base de datos
CREATE DATABASE IF NOT EXISTS smart_gym
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE smart_gym;

-- =========================================================
-- 1) Tablas
-- =========================================================

CREATE TABLE IF NOT EXISTS gyms (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  address VARCHAR(200),
  phone VARCHAR(30),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
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

CREATE TABLE IF NOT EXISTS activity_types (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL UNIQUE,
  description VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS classes (
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

CREATE TABLE IF NOT EXISTS class_reservations (
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

CREATE TABLE IF NOT EXISTS membership_plans (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL UNIQUE,
  duration_days SMALLINT UNSIGNED NOT NULL,
  price DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_memberships (
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

CREATE TABLE IF NOT EXISTS payments (
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

CREATE TABLE IF NOT EXISTS routines (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  type VARCHAR(60),
  level ENUM('BEGINNER','INTERMEDIATE','ADVANCED') NOT NULL DEFAULT 'BEGINNER',
  goal  VARCHAR(160),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY ix_routines_user (user_id),
  CONSTRAINT fk_routines_user
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS routine_items (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  routine_id BIGINT UNSIGNED NOT NULL,
  activity_type_id BIGINT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  reps VARCHAR(40),
  sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  KEY ix_ri_routine (routine_id, sort_order),
  CONSTRAINT fk_ri_routine
    FOREIGN KEY (routine_id) REFERENCES routines(id)
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_ri_activity
    FOREIGN KEY (activity_type_id) REFERENCES activity_types(id)
      ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS body_measurements (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  measured_at DATE NOT NULL,
  weight_kg DECIMAL(5,2),
  height_m  DECIMAL(3,2),
  body_fat_percent DECIMAL(4,1),
  bmi DECIMAL(5,2) GENERATED ALWAYS AS
      (CASE WHEN height_m IS NULL OR height_m=0 THEN NULL
            ELSE weight_kg/(height_m*height_m) END) VIRTUAL,
  UNIQUE KEY uq_bm_user_date (user_id, measured_at),
  CONSTRAINT fk_bm_user
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS posts (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NULL,
  title VARCHAR(160) NOT NULL,
  body TEXT NOT NULL,
  published_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY ix_posts_pub (published_at),
  CONSTRAINT fk_posts_user
    FOREIGN KEY (user_id) REFERENCES users(id)
      ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabla TRAINERS (si no existía)
CREATE TABLE IF NOT EXISTS trainers (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  gym_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  specialty VARCHAR(80),
  phone VARCHAR(30),
  email VARCHAR(160),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_trainers_gym
    FOREIGN KEY (gym_id) REFERENCES gyms(id)
      ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- Extensión de columnas nuevas para TRAINERS
ALTER TABLE trainers
  ADD COLUMN IF NOT EXISTS description TEXT NULL,
  ADD COLUMN IF NOT EXISTS image_url VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS facebook VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS twitter  VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS instagram VARCHAR(255) NULL;

-- =========================================================
-- 2) Vistas
-- =========================================================
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

-- =========================================================
-- 3) Datos semilla (idempotentes)
-- =========================================================

-- GYM base
INSERT INTO gyms(name,address,phone)
SELECT 'Smart Gym Central','San José','+506 0000-0000'
WHERE NOT EXISTS (SELECT 1 FROM gyms WHERE name='Smart Gym Central');

-- Actividades
INSERT INTO activity_types(name,description)
SELECT 'Yoga','Posturas y respiración'
WHERE NOT EXISTS (SELECT 1 FROM activity_types WHERE name='Yoga');

INSERT INTO activity_types(name,description)
SELECT 'CrossFit','Alta intensidad'
WHERE NOT EXISTS (SELECT 1 FROM activity_types WHERE name='CrossFit');

INSERT INTO activity_types(name,description)
SELECT 'Ballet','Técnica y danza'
WHERE NOT EXISTS (SELECT 1 FROM activity_types WHERE name='Ballet');

-- Planes
INSERT INTO membership_plans(name,duration_days,price)
SELECT 'Basic',30,29.00
WHERE NOT EXISTS (SELECT 1 FROM membership_plans WHERE name='Basic');

INSERT INTO membership_plans(name,duration_days,price)
SELECT 'Pro',30,59.00
WHERE NOT EXISTS (SELECT 1 FROM membership_plans WHERE name='Pro');

INSERT INTO membership_plans(name,duration_days,price)
SELECT 'Elite',30,99.00
WHERE NOT EXISTS (SELECT 1 FROM membership_plans WHERE name='Elite');

-- Entrenadores (dos ejemplos, seguros contra duplicados por nombre)
INSERT INTO trainers (gym_id, name, specialty, phone, email)
SELECT 1,'Carlos Ramírez','Yoga','+506 8888-8888','carlos@smartgym.com'
WHERE NOT EXISTS (SELECT 1 FROM trainers WHERE name='Carlos Ramírez');

INSERT INTO trainers (gym_id, name, specialty, phone, email)
SELECT 1,'Ana Gómez','CrossFit','+506 7777-7777','ana@smartgym.com'
WHERE NOT EXISTS (SELECT 1 FROM trainers WHERE name='Ana Gómez');

-- Imágenes para entrenadores (actualiza si existen)
UPDATE trainers
SET image_url = 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1470&q=80'
WHERE name = 'Carlos Ramírez';

UPDATE trainers
SET image_url = 'https://images.unsplash.com/photo-1545205597-3d9d02c29597?auto=format&fit=crop&w=1470&q=80'
WHERE name = 'Ana Gómez';
ALTER TABLE posts ADD COLUMN image_url VARCHAR(255) NULL;
UPDATE posts
SET image_url='https://images.unsplash.com/photo-1545205597-3d9d02c29597?auto=format&fit=crop&w=1470&q=80'
WHERE id=1;

ALTER TABLE classes ADD COLUMN image_url VARCHAR(255) NULL;


UPDATE classes c JOIN activity_types a ON a.id=c.activity_type_id
SET c.image_url='https://images.unsplash.com/photo-1545205597-3d9d02c29597?auto=format&fit=crop&w=1470&q=80'
WHERE a.name='CrossFit';

UPDATE classes c JOIN activity_types a ON a.id=c.activity_type_id
SET c.image_url='https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1470&q=80'
WHERE a.name='Yoga';

CREATE TABLE IF NOT EXISTS gallery_images (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NULL,
  filename VARCHAR(255) NOT NULL,
  title VARCHAR(160) NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY ix_sort (sort_order),
  CONSTRAINT fk_gallery_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS uploads (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NULL,
  filename VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_uploads_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;