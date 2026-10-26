Office Inventory Management System

A web-based inventory management application built with Laravel 12, Filament 3, and MySQL to manage employees, office assets, equipment assignments, and projects through a centralized administration panel.

Tech Stack

* Backend: PHP 8.4 · Laravel 12 · MySQL 8.1
* Admin Panel: Filament 3 · Livewire
* Authentication & Authorization: Laravel Sanctum · Filament Shield · Spatie Permission
* Infrastructure: Docker · Nginx · Redis
* Testing & CI: Pest · GitHub Actions

Features

* Employee Management: Create, update, and manage employee records.
* Asset Tracking: Manage office equipment, serial numbers, and availability.
* Equipment Assignment: Assign assets to employees and track equipment ownership.
* Project Management: Associate employees with multiple projects.
* Role-Based Access Control: Manage access permissions using Filament Shield and Laravel policies.
* Dashboard & Analytics: Visualize inventory statistics through charts and widgets.
* JSON Import/Export: Import and export employee records with related assets and projects.
* REST API: Token-based authentication and protected asset endpoints using Laravel Sanctum.
* Assignment Documents: Generate printable equipment assignment records.

Screenshots

Dashboard

Employee Management

Asset Management

User Management

Roles & Permissions

Getting Started

Requirements

* Docker
* Docker Compose

Installation

1. Clone the repository

git clone https://github.com/Zehraatalay/office-inventory-management.git
cd office-inventory-management

2. Configure the environment

cp .env.example .env

Configure the database credentials and required environment variables in .env before starting the containers.

3. Start Docker containers

docker compose up -d --build

4. Install dependencies

docker compose exec php composer install

5. Initialize the application

docker compose exec php php artisan key:generate
docker compose exec php php artisan migrate
docker compose exec php php artisan db:seed
docker compose exec php php artisan filament:assets

The database seeder initializes the application’s roles and permissions.

Administrator Setup

Create a Filament user:

docker compose exec php php artisan make:filament-user

Assign the super_admin role to the newly created account.

Open Laravel Tinker:

docker compose exec php php artisan tinker

Run the following commands, replacing admin@example.com with your administrator email address:

$user = \App\Models\User::where('email', 'admin@example.com')->firstOrFail();
$user->assignRole('super_admin');

Exit Tinker:

exit

Available Roles

Role	Access
super_admin	Full administrative access
Stajyer	Read-only access to asset records

No default administrator credentials are included.

Access

Once the setup is complete, open the administration panel:

http://localhost:8000/admin

API Endpoints

Method	Endpoint	Description
POST	/api/login	User authentication
POST	/api/mobile-login	Mobile authentication
GET	/api/user	Retrieve authenticated user
POST	/api/logout	Revoke authentication token
GET	/api/assets	Retrieve authorized asset records

Protected endpoints use Laravel Sanctum authentication.

The asset listing endpoint requires a valid personal access token with the server:read ability and the appropriate asset-viewing permission.

Testing

The application includes automated tests for authentication, API authorization, token abilities, and Laravel policies.

Run the test suite:

docker compose exec php php artisan test

Tests use an isolated SQLite environment. The GitHub Actions workflow runs the test suite automatically on pushes and pull requests to main.

Acknowledgments

The Docker environment was initially based on the Laravel Docker Starter Kit by Refactorian and extended with the application’s inventory management features, administrative resources, and authorization logic.