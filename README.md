# Sistema Administrativo Web

Sistema web de gestión académica desarrollado en **PHP + MySQL + Bootstrap 5**.
Incluye autenticación de usuarios, manejo de sesiones, encriptación de contraseñas
(bcrypt) y CRUD completo para los módulos: **Usuarios, Estudiantes, Cursos y Matrículas**.

## Requisitos

- PHP 7.4 o superior (con extensión PDO y pdo_mysql habilitada)
- MySQL 5.7+ / MariaDB 10.3+
- Servidor web local: XAMPP, WAMP, Laragon o `php -S`

## Instalación

1. **Copiar el proyecto**
   Coloca la carpeta `sistema_administrativo/` dentro de tu directorio del servidor
   (por ejemplo `htdocs/` en XAMPP o `www/` en WAMP).

2. **Crear la base de datos**
   Abre phpMyAdmin (o la consola de MySQL) e importa el script `database.sql`:
   ```bash
   mysql -u root -p < database.sql
   ```
   Esto creará la base de datos `sistema_administrativo`, todas las tablas, la vista
   `vista_matriculas`, y algunos datos de ejemplo (incluyendo el usuario administrador).

3. **Configurar la conexión**
   Edita `config/database.php` si tu usuario/contraseña de MySQL son distintos a los
   valores por defecto (`root` sin contraseña):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'sistema_administrativo');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Levantar el servidor**
   - Con XAMPP/WAMP: inicia Apache y MySQL, y abre `http://localhost/sistema_administrativo/login.php`
   - O con el servidor embebido de PHP, desde la carpeta del proyecto:
     ```bash
     php -S localhost:8000
     ```
     y abre `http://localhost:8000/login.php`

## Credenciales de prueba

| Correo               | Contraseña |
|-----------------------|------------|
| admin@sistema.com     | admin123   |

## Estructura del proyecto

```
sistema_administrativo/
├── config/database.php        Conexión PDO a MySQL
├── includes/
│   ├── funciones.php          Sesión, seguridad, helpers (CSRF, mensajes flash)
│   ├── header.php              Navbar + apertura de layout Bootstrap
│   └── footer.php               Cierre de layout
├── login.php / logout.php      Autenticación
├── index.php                    Dashboard con estadísticas (COUNT)
├── usuarios/       index.php · form.php · guardar.php · eliminar.php
├── estudiantes/    index.php · form.php · guardar.php · eliminar.php
├── cursos/         index.php · form.php · guardar.php · eliminar.php
├── matriculas/     index.php · form.php · guardar.php · eliminar.php
├── assets/css/style.css
└── database.sql                 Script completo de la base de datos
```

## Características de seguridad

- Contraseñas encriptadas con `password_hash()` (bcrypt) y verificadas con `password_verify()`.
- Todas las consultas usan **prepared statements** de PDO (`PDO::ATTR_EMULATE_PREPARES = false`).
- Protección **CSRF** en todos los formularios (token de sesión validado en cada POST).
- Salida escapada con `htmlspecialchars()` para prevenir XSS.
- Control de acceso por sesión (`requerirLogin()`) en todos los módulos.
- Regeneración de ID de sesión tras el login para prevenir fijación de sesión.

## Base de datos

- 4 tablas principales: `usuarios`, `estudiantes`, `cursos`, `matriculas`.
- Relación N:M entre `estudiantes` y `cursos` resuelta con la tabla intermedia `matriculas`
  (claves foráneas con `ON DELETE CASCADE`).
- Vista `vista_matriculas`: une las 3 tablas con `INNER JOIN` para reportes y listados.
- Restricción única `UQ(estudiante_id, curso_id)` para evitar doble matrícula en el mismo curso.
- Índices adicionales sobre campos de búsqueda frecuente.

Consulta el archivo `Modelo_Relacional_Sistema_Administrativo.pdf` para el diagrama
entidad-relación completo y el diccionario de datos.
