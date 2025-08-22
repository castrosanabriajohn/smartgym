# 🏋️‍♂️ SmartGym

Aplicación web para la gestión de gimnasios construida con **PHP 8**, **MySQL/MariaDB** y **TailwindCSS**. Incluye autenticación de usuarios, reservas de clases, planes de membresía, control de pagos, blog/noticias y más.

## Estructura del proyecto

```
smartgym/
├── css/              # Estilos CSS
├── media/            # Imágenes y otros recursos multimedia
├── uploads/          # Archivos subidos por los usuarios
├── includes/         # Vistas compartidas (header.php, footer.php)
├── pages/            # Páginas internas (login, registro, clases, etc.)
├── database/         # Scripts SQL
│   └── smart_gym_schema.sql
├── config.php        # Conexión a la base de datos
├── init_db.php       # Script opcional para crear tablas auxiliares y datos de prueba
├── index.php         # Página principal
├── composer.json     # Dependencias PHP (PHPMailer)
└── README.md
```

## Requisitos

- PHP >= 8.0
- MySQL/MariaDB >= 10.4
- Composer
- Servidor web (Apache2 o XAMPP recomendado)

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/TU_USUARIO/smartgym.git
   cd smartgym
   ```
2. **Instalar dependencias PHP**
   ```bash
   composer install
   ```
3. **Configurar la base de datos**
   - Crear una base de datos vacía en MySQL/MariaDB.
   - Importar el esquema:
     ```sql
     SOURCE database/smart_gym_schema.sql;
     ```
4. **Actualizar credenciales en `config.php`**
5. *(Opcional)* **Ejecutar seeder inicial**
   ```bash
   php init_db.php
   ```
6. **Abrir en el navegador**
   ```
   http://localhost/smartgym
   ```

## Licencia

Proyecto de uso educativo y demostrativo. Siéntete libre de adaptarlo a tus necesidades.

