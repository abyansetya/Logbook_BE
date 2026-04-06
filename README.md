# Logbook Backend

## Overview
The Logbook backend provides a robust RESTful API that handles data persistence, authentication, and core domain logic for the Logbook application. It is built using the Laravel PHP framework and offers secure and efficient data management.

## Technology Stack
- **Framework:** Laravel 12
- **Language:** PHP 8.2+
- **Authentication:** Laravel Sanctum
- **Database:** MySQL
- **Export Capabilities:** PhpSpreadsheet

## Prerequisites
Ensure the following requirements are met before proceeding:
- PHP >= 8.2
- Composer
- A supported relational database system (e.g., MySQL, PostgreSQL, or SQLite)

## Installation
1. Navigate to the backend directory:
   ```bash
   cd backend
   ```
2. Install PHP dependencies via Composer:
   ```bash
   composer install
   ```

## Configuration
1. Copy the example environment file:
   ```bash
   cp .env.example .env
   ```
2. Generate an application key:
   ```bash
   php artisan key:generate
   ```
3. Update the `.env` file with your database credentials and application settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=logbook
   DB_USERNAME=root
   DB_PASSWORD=
   
   APP_URL=http://127.0.0.1:8000
   ```

## Database Migration and Seeding
To run the database migrations and populate the database with initial records, execute:
```bash
php artisan migrate --seed
```

## Storage Linking
File uploads necessitate linking the storage directory to the public path so that file resources can be accessed correctly by the frontend client:
```bash
php artisan storage:link
```

## Running the Application
To start the local development server, run:
```bash
php artisan serve
```
The API will be accessible at `http://127.0.0.1:8000`.

## Testing
To run the integrated test suite:
```bash
php artisan test
```

## License
Proprietary. All rights reserved.
