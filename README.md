============================================================
🏋️‍♂️ SMARTGYM - Proyecto PHP/MySQL
============================================================

Aplicación web para la gestión de gimnasios, construida en 
PHP 8, MySQL/MariaDB y TailwindCSS, pensada para correr en 
XAMPP o LAMP.

Incluye:
- Registro e inicio de sesión de usuarios
- Reservas de clases
- Planes de membresía
- Control de pagos
- Rutinas personalizadas
- Blog / noticias
- Base de datos robusta con llaves foráneas y vistas

------------------------------------------------------------
📂 ESTRUCTURA DEL PROYECTO
------------------------------------------------------------
smartgym/
├── assets/                 -> Estilos, imágenes, JS
├── includes/               -> Archivos compartidos (db.php, header.php, footer.php)
├── pages/                  -> Páginas internas (login, register, etc.)
├── database/               -> Scripts SQL
│   └── smart_gym.sql       -> Estructura completa de la BD con datos semilla
├── index.php               -> Página principal
└── README.md

------------------------------------------------------------
⚙️ REQUISITOS
------------------------------------------------------------
- PHP >= 8.0  
- MySQL/MariaDB >= 10.4  
- Apache2  
- XAMPP (recomendado)

------------------------------------------------------------
🚀 INSTALACIÓN
------------------------------------------------------------
1. Clonar repositorio
   git clone https://github.com/TU_USUARIO/smartgym.git
   Copiar carpeta dentro de htdocs/ de XAMPP:
   C:\xampp\htdocs\smartgym

2. Configurar base de datos
   Abrir phpMyAdmin y ejecutar:
   SOURCE database/smart_gym.sql;

3. Editar conexión en includes/db.php
   <?php
   $db = new PDO(
       'mysql:host=localhost;dbname=smart_gym;charset=utf8mb4',
       'root',   // usuario MySQL
       ''        // contraseña (vacía por defecto en XAMPP)
   );
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   session_start();
   ?>

4. Abrir en navegador:
   http://localhost/smartgym

------------------------------------------------------------
🗄️ BASE DE DATOS - ESTRUCTURA
------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS smart_gym
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE smart_gym;

-- (Tablas: gyms, users, activity_types, classes, class_reservations,
-- membership_plans, user_memberships, payments, routines,
-- routine_items, body_measurements, posts)

-- Vistas:
vw_class_spots
vw_active_memberships

-- Datos semilla:
INSERT INTO gyms(name,address,phone)
VALUES ('Smart Gym Central','San José','+506 0000-0000');

INSERT INTO activity_types(name,description)
VALUES ('Yoga','Posturas y respiración'),
       ('CrossFit','Alta intensidad'),
       ('Ballet','Técnica y danza');

INSERT INTO membership_plans(name,duration_days,price)
VALUES ('Basic',30,29.00),
       ('Pro',30,59.00),
       ('Elite',30,99.00);

------------------------------------------------------------
👤 FUNCIONALIDADES
------------------------------------------------------------
- Registro / login con contraseñas encriptadas
- Reservar clases según cupo disponible
- Gestión de membresías y estado
- Pagos asociados a membresías o reservas
- Noticias/blog para el gimnasio

------------------------------------------------------------
📜 LICENCIA
------------------------------------------------------------
Proyecto de uso educativo y demostrativo.
Puedes adaptarlo a tus necesidades.
============================================================
