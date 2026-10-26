Office Inventory Management System

A web-based inventory management application built with Laravel 12, Filament 3, and MySQL to manage employees, office assets, equipment assignments, and projects through a centralized admin panel.

Tech Stack

Backend: PHP 8.4 · Laravel 12 · MySQL 8.1
Admin Panel: Filament 3 · Livewire
Authentication & Authorization: Laravel Sanctum · Filament Shield · Spatie Permission
Infrastructure: Docker · Nginx · Redis

Features

* Employee Management: Create, update, and manage employee records.
* Asset Tracking: Manage office equipment, serial numbers, and availability.
* Equipment Assignment: Assign assets to employees and track ownership.
* Project Management: Associate employees with multiple projects.
* Role-Based Access Control: Manage permissions using Filament Shield and Laravel policies.
* Dashboard & Analytics: Visualize inventory statistics through charts and widgets.
* JSON Import/Export: Import and export employee records with related assets and projects.
* REST API: Token-based authentication and protected asset endpoints using Laravel Sanctum.
* Assignment Documents: Generate printable equipment assignment records.

Screenshots

Dashboard

Employee Management

Asset Management

Roles & Permissions

Getting Started

Requirements

* Docker
* Docker Compose

Installation

git clone https://github.com/Zehraatalay/office-inventory-management.git
cd office-inventory-management
cp .env.example .env
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate
docker compose exec php php artisan filament:assets

Configure your database credentials in .env before starting the containers.

Create an admin user:

docker compose exec php php artisan make:filament-user

Note: Admin panel access requires the appropriate role (super_admin or Stajyer). Assign roles and configure permissions before logging in.

Access the admin panel at http://localhost:8000/admin.

API Endpoints

Method	Endpoint	Description
POST	/api/login	User authentication
POST	/api/mobile-login	Mobile authentication
GET	/api/user	Authenticated user
POST	/api/logout	Token revocation
GET	/api/assets	Authorized asset listing

Protected endpoints use Laravel Sanctum authentication.

Acknowledgments

The Docker environment was initially based on the Laravel Docker Starter Kit by Refactorian and extended with the application’s inventory management features, administrative resources, and authorization logic.

Administrator Role Setup

After creating a Filament user, assign the super_admin role to enable access to the administration panel.

Open Laravel Tinker:

docker compose exec php php artisan tinker

Assign the role to your administrator account:

$user = \App\Models\User::where('email', 'admin@example.com')->firstOrFail();
$user->assignRole('super_admin');

Replace admin@example.com with the email address used during account creation.

The application includes two predefined roles:

* super_admin: Full administrative access.
* Stajyer: Read-only access to asset records.

Roles and permissions are initialized through the database seeder. No default administrator credentials are included.