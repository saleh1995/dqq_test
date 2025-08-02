# Inventory & Stock Transfer API

This project is a Laravel-based RESTful API for managing companies, warehouses, products, users, and stock transfers between warehouses. It provides secure authentication, CRUD operations, and a robust workflow for stock transfer management.

---

## Table of Contents

-   [Features](#features)
-   [API Documentation](#api-documentation)
-   [Getting Started](#getting-started)
-   [Usage](#usage)
-   [Project Structure](#project-structure)
-   [Contributing](#contributing)
-   [License](#license)

---

## Features

-   User registration, login, and token-based authentication (Laravel Sanctum)
-   Company management (CRUD)
-   Warehouse management (CRUD)
-   Product management (CRUD)
-   Stock transfer management between warehouses with status workflow
-   Role-based access for stock transfer actions
-   Consistent API responses and error handling
-   Database seeding for demo users, companies, warehouses, and products

---

## API Documentation

-   [Authentication API](AUTH_API.md)
-   [Company API](COMPANY_API.md)
-   [Warehouse API](WAREHOUSE_API.md)
-   [Product API](PRODUCT_API.md)
-   [Stock Transfer API](STOCK_TRANSFER_API.md)
-   [Postman Collection](https://documenter.getpostman.com/view/21836217/2sB3BAMYG5)

Each API doc contains detailed endpoint descriptions, request/response examples, and error handling.

---

## Getting Started

### Prerequisites

-   PHP >= 8.2
-   Composer
-   MySQL or compatible database

### Installation

1. Clone the repository:
    ```bash
    git clone <your-repo-url>
    cd <project-directory>
    ```
2. Install dependencies:
    ```bash
    composer install
    ```
3. Copy the example environment file and set your configuration:
    ```bash
    cp .env.example .env
    # Edit .env as needed
    ```
4. Generate application key:
    ```bash
    php artisan key:generate
    ```
5. Run migrations and seeders:
    ```bash
    php artisan migrate
    php artisan db:seed
    ```
6. (Optional) Install Node dependencies and build assets:
    ```bash
    npm install && npm run dev
    ```
7. Start the development server:
    ```bash
    php artisan serve
    ```

---

## Usage

-   Register or login to obtain a Bearer token.
-   Use the token in the `Authorization` header for all protected endpoints:
    ```
    Authorization: Bearer {your_token}
    ```
-   Refer to the API documentation files for endpoint details and example requests.

---

## Project Structure

-   `app/Models/` - Eloquent models
-   `app/Http/Controllers/Api/` - API controllers
-   `app/Services/` - Business logic services
-   `app/Http/Requests/` - Request validation
-   `app/Http/Resources/` - API resource transformers
-   `app/Traits/` - Shared traits (e.g., ApiResponseTrait)
-   `database/seeders/` - Database seeders
-   `routes/api.php` - API route definitions

---

## Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
