# Movies API - Backend

API RESTful desarrollada con Laravel 11 para gestión de películas con sistema de autenticación y reviews.

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)](https://mysql.com)

## Demo en Vivo

**[Ver API](https://movies-api-laravel-production-61fb.up.railway.app)** | **[Ver Frontend](https://movies-app-angular-three.vercel.app/)**

---

## Características

### Autenticación
- Sistema de autenticación con Laravel Sanctum
- Registro e inicio de sesión de usuarios
- Tokens API para autenticación stateless
- Protección de rutas con middleware

### API de Películas
- CRUD completo de películas
- Subida y almacenamiento de imágenes
- Soft deletes para eliminación segura
- Relaciones entre usuarios y películas

### Sistema de Reviews
- Calificaciones de 1 a 5 estrellas
- Comentarios de texto
- Relación usuario-película-review
- Validación de datos

### Seguridad
- Validación de entrada de datos
- Protección contra inyección SQL
- CORS configurado
- Encriptación de contraseñas

---

## Stack Tecnológico

- **Laravel 11** - Framework PHP
- **MySQL 8** - Base de datos relacional
- **Laravel Sanctum** - Autenticación API
- **Docker** - Containerización
- **Nginx** - Servidor web
- **PHP-FPM** - Procesador PHP

---

## Estructura de la Base de Datos

### Tabla `users`
```sql
id          - INT PRIMARY KEY
username    - VARCHAR(255) UNIQUE
password    - VARCHAR(255)
created_at  - TIMESTAMP
updated_at  - TIMESTAMP
```

### Tabla `movies`
```sql
id            - INT PRIMARY KEY
user_id       - INT FOREIGN KEY
title         - VARCHAR(255)
description   - TEXT
poster        - VARCHAR(255)
is_published  - BOOLEAN
created_at    - TIMESTAMP
updated_at    - TIMESTAMP
deleted_at    - TIMESTAMP (soft delete)
```

### Tabla `reviews`
```sql
id          - INT PRIMARY KEY
movie_id    - INT FOREIGN KEY
user_id     - INT FOREIGN KEY
rating      - INT (1-5)
comment     - TEXT
created_at  - TIMESTAMP
updated_at  - TIMESTAMP
```

---

## API Endpoints

### Autenticación (Públicos)

**Registro de Usuario**
```http
POST /api/register
Content-Type: application/json

{
  "username": "string",
  "password": "string"
}

Response: 201 Created
{
  "success": true,
  "message": "Usuario registrado correctamente",
  "data": {
    "token": "string",
    "username": "string"
  }
}
```

**Inicio de Sesión**
```http
POST /api/login
Content-Type: application/json

{
  "username": "string",
  "password": "string"
}

Response: 200 OK
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "token": "string",
    "username": "string"
  }
}
```

### Películas (Requieren autenticación)

**Listar Películas**
```http
GET /api/movies
Authorization: Bearer {token}

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "title": "string",
      "description": "string",
      "poster": "string",
      "is_published": boolean,
      "user_id": 1
    }
  ]
}
```

**Crear Película**
```http
POST /api/movies
Authorization: Bearer {token}
Content-Type: multipart/form-data

title: string (required)
description: string (optional)
poster: file (optional, max 2MB)

Response: 201 Created
{
  "message": "Película creada correctamente",
  "data": { movie object }
}
```

**Obtener Película**
```http
GET /api/movies/{id}
Authorization: Bearer {token}

Response: 200 OK
{
  "data": { movie object }
}
```

**Actualizar Película**
```http
PUT /api/movies/{id}
Authorization: Bearer {token}
Content-Type: multipart/form-data

title: string (optional)
description: string (optional)
poster: file (optional)

Response: 200 OK
{
  "message": "Película actualizada correctamente",
  "data": { movie object }
}
```

**Eliminar Película**
```http
DELETE /api/movies/{id}
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "Película eliminada correctamente (soft delete)"
}
```

### Reviews (Requieren autenticación)

**Listar Reviews de una Película**
```http
GET /api/movies/{movie_id}/reviews
Authorization: Bearer {token}

Response: 200 OK
{
  "data": [
    {
      "id": 1,
      "movie_id": 1,
      "user_id": 1,
      "rating": 5,
      "comment": "string",
      "user": {
        "id": 1,
        "username": "string"
      }
    }
  ]
}
```

**Crear Review**
```http
POST /api/movies/{movie_id}/reviews
Authorization: Bearer {token}
Content-Type: application/json

{
  "rating": 5,
  "comment": "string"
}

Response: 201 Created
{
  "message": "Review creada correctamente",
  "data": { review object }
}
```

---

## Instalación y Uso

### Prerrequisitos
- PHP 8.2 o superior
- Composer
- MySQL 8 o superior
- Docker (opcional)

### Instalación Local
```bash
# Clonar repositorio
git clone https://github.com/Juanjt01/movies-api-laravel.git
cd movies-api-laravel

# Instalar dependencias
composer install

# Configurar entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=movies_api
DB_USERNAME=root
DB_PASSWORD=

# Ejecutar migraciones
php artisan migrate

# Crear enlace de almacenamiento
php artisan storage:link

# Iniciar servidor
php artisan serve
```

La API estará disponible en `http://localhost:8000`

### Docker
```bash
# Construir y levantar contenedores
docker-compose up --build

# La API estará en http://localhost:8080
```

---

## Configuración de Docker

### Dockerfile
```dockerfile
FROM php:8.2-fpm-alpine

# Instalación de dependencias del sistema
RUN apk add --no-cache \
    curl \
    netcat-openbsd \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor

# Instalación de extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Configuración de Nginx y PHP-FPM
# ...
```

### docker-compose.yml
```yaml
services:
  backend:   # Laravel API (puerto 8080)
  frontend:  # Angular App (puerto 80)
  db:        # MySQL Database (puerto 3307)
```

---

## Variables de Entorno
```env
APP_NAME=MoviesAPI
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://movies-api-laravel-production-61fb.up.railway.app

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=movies_api
DB_USERNAME=movies_user
DB_PASSWORD=secret
```

---

## Arquitectura del Proyecto
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php      # Autenticación
│   │   ├── MovieController.php     # CRUD películas
│   │   └── ReviewController.php    # Reviews
│   └── Middleware/
├── Models/
│   ├── User.php                    # Modelo de usuario
│   ├── Movie.php                   # Modelo de película
│   └── Review.php                  # Modelo de review
database/
├── migrations/
│   ├── create_users_table
│   ├── create_movies_table
│   ├── create_reviews_table
│   └── add_user_id_to_movies_table
routes/
└── api.php                         # Rutas de la API
```

---

## Deployment

### Railway
1. Conectar repositorio de GitHub
2. Configurar variables de entorno
3. Agregar servicio MySQL
4. Deploy automático desde branch `main`

### Servicios Configurados
- **Web Service**: Laravel API en contenedor Docker
- **Database**: MySQL 8 con persistencia
- **Storage**: Volumen persistente para imágenes subidas

---

## Seguridad

### Autenticación con Sanctum
```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('movies', MovieController::class);
    // ...
});
```

### Validación de Datos
```php
$validated = $request->validate([
    'title' => 'required|string|max:100',
    'description' => 'nullable|string',
    'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
]);
```

### CORS Configurado
```php
// config/cors.php
'allowed_origins' => ['https://movies-app-angular-three.vercel.app'],
```

---

## Repositorios Relacionados

- **Frontend Angular**: [movies-app-angular](https://github.com/Juanjt01/movies-app-angular)
- **Deploy Frontend**: [Vercel](https://movies-app-angular-three.vercel.app/)
- **Deploy Backend**: [Railway](https://movies-api-laravel-production-61fb.up.railway.app)

---

## Desarrollado Por

**Juan Terán**

- GitHub: [@Juanjt01](https://github.com/Juanjt01)
- LinkedIn: [Juan Terán](https://www.linkedin.com/in/juan-jos%C3%A9-ter%C3%A1n-triana-b33924261/)

---

## Licencia

Este proyecto fue creado con fines educativos y de demostración de habilidades técnicas.
