Office Inventory Management System

A web-based office inventory and asset assignment management system built with Laravel 12, Filament 3, and MySQL.

The application provides a centralized interface for managing employees, office equipment, asset assignments, and projects. It includes role-based access control, dashboard visualizations, JSON data import/export, and token-based API authentication.

Features

Employee Management

* Create, view, update, and delete employee records.
* Manage employee information and assigned equipment.
* Associate employees with multiple projects.
* Import employee data from JSON files.
* Export employee records, assets, and project associations to JSON.

Asset Management

* Track office equipment, including laptops, monitors, phones, and peripherals.
* Store asset details and serial numbers.
* Assign equipment to employees.
* Monitor asset availability and assignment status.
* Manage asset records through the Filament admin panel.

Project Management

* Create and manage projects.
* Associate multiple employees with projects.
* Maintain employee–project relationships.

Dashboard and Analytics

* Administrative dashboard with inventory statistics.
* Data visualizations using Filament widgets.
* Charts for monitoring inventory-related information.

Authentication and Authorization

* Authentication through the Filament admin panel.
* Role-based access control using Filament Shield and Spatie Laravel Permission.
* Resource-level authorization through Laravel policies.
* API authentication using Laravel Sanctum.
* Token ability restrictions and login rate limiting.

JSON Import and Export

* Import employee records and their associated data.
* Validate imported records.
* Use database transactions to protect data consistency.
* Export structured employee information in JSON format.

Technology Stack

Component	Technology
Backend	PHP 8.4, Laravel 12
Admin Panel	Filament 3
Database	MySQL 8.1
Authentication	Laravel Sanctum
Authorization	Filament Shield, Spatie Permission
Web Server	Nginx
Containerization	Docker, Docker Compose
Frontend Tooling	Vite, Node.js
Supporting Services	Redis, Mailpit

Screenshots

Dashboard

Employee Management

Asset Management

Role Management

User Management

Screenshots should contain demonstration data only. Real employee and user information must be removed before publication.

Getting Started

Prerequisites

* Docker
* Docker Compose
* Git

Installation

1. Clone the repository

git clone https://github.com/Zehraatalay/office-inventory-management.git
cd office-inventory-management

2. Configure the environment

cp .env.example .env

Update the database credentials and other environment variables in .env as needed.

Ensure the database configuration matches the Docker Compose services.

3. Build and start the containers

docker compose up -d --build

4. Install PHP dependencies

docker compose exec php composer install

5. Generate the application key

docker compose exec php php artisan key:generate

6. Run database migrations

docker compose exec php php artisan migrate

7. Publish Filament assets

docker compose exec php php artisan filament:assets

8. Create an administrator account

docker compose exec php php artisan make:filament-user

Configure the administrator’s role and permissions before using protected resources.

Application URLs

Service	URL
Laravel / Filament	http://localhost:8000/admin
phpMyAdmin	http://localhost:8080
Adminer	http://localhost:9090
Mailpit	http://localhost:8025

API

The application includes a token-authenticated API using Laravel Sanctum.

Method	Endpoint	Description
POST	/api/login	Authenticate a user
POST	/api/mobile-login	Authenticate a mobile client
GET	/api/user	Retrieve the authenticated user
POST	/api/logout	Revoke the current API token
GET	/api/assets	Retrieve a limited list of assets

Protected endpoints require authentication. Asset listing additionally checks token abilities and resource authorization.

Project Structure

app/
├── Filament/
│   ├── Resources/
│   └── Widgets/
├── Http/
│   └── Controllers/
│       └── Api/
├── Models/
├── Policies/
└── Providers/
database/
└── migrations/
resources/
└── views/
routes/
├── api.php
└── web.php
docs/
└── screenshots/
docker-compose.yml

Security Notes

* Environment credentials are stored in .env, which is excluded from version control.
* API authentication uses Laravel Sanctum.
* Resource authorization is implemented using Laravel policies and Spatie Permission.
* Import/export operations should be restricted to authorized users.
* Real employee data, authentication tokens, and database backups must not be committed to the repository.

Development Background

This application was developed as an office inventory management project using Laravel and Filament.

The Docker environment was initially based on the Laravel Docker Starter Kit by Refactorian and was extended with application-specific models, database relationships, administrative resources, authorization policies, dashboards, and inventory management functionality.