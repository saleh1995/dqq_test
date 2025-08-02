# Authentication API Documentation

This document describes the Authentication API endpoints implemented using Laravel Sanctum for token-based authentication.

## Base URL

```
/api
```

## Endpoints

### 1. User Registration

**POST** `/api/register`

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**

```json
{
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2025-08-02T15:00:00.000000Z",
            "updated_at": "2025-08-02T15:00:00.000000Z"
        },
        "token": "1|abc123def456...",
        "token_type": "Bearer"
    },
    "message": "Registration successful",
    "status": 201,
    "success": true
}
```

### 2. User Login

**POST** `/api/login`

**Request Body:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**

```json
{
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2025-08-02T15:00:00.000000Z",
            "updated_at": "2025-08-02T15:00:00.000000Z"
        },
        "token": "1|abc123def456...",
        "token_type": "Bearer"
    },
    "message": "Login successful",
    "status": 200,
    "success": true
}
```

### 3. Get Authenticated User

**GET** `/api/user`

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2025-08-02T15:00:00.000000Z",
        "updated_at": "2025-08-02T15:00:00.000000Z"
    },
    "message": "User retrieved successfully",
    "status": 200,
    "success": true
}
```

### 4. User Logout

**POST** `/api/logout`

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
    "data": null,
    "message": "Logout successful",
    "status": 200,
    "success": true
}
```

## Error Responses

### Validation Error (422)

```json
{
    "data": null,
    "message": "ERR_006",
    "status": 422,
    "success": false,
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Invalid Credentials (401)

```json
{
    "data": null,
    "message": "Invalid credentials",
    "status": 401,
    "success": false
}
```

### Duplicate Email (422)

```json
{
    "data": null,
    "message": "A user with this email already exists",
    "status": 422,
    "success": false
}
```

### Unauthorized (401)

```json
{
    "data": null,
    "message": "ERR_001",
    "status": 401,
    "success": false
}
```

## Implementation Details

### Architecture

-   **Controller**: `App\Http\Controllers\Api\AuthController`
-   **Model**: `App\Models\User` (with Sanctum support)
-   **Resource**: `App\Http\Resources\UserResource`
-   **Request Validation**: `App\Http\Requests\LoginRequest`, `App\Http\Requests\RegisterRequest`
-   **Response Trait**: `App\Traits\ApiResponseTrait`
-   **Authentication**: Laravel Sanctum

### Features

-   ✅ User registration with validation
-   ✅ User login with token generation
-   ✅ Token-based authentication
-   ✅ User profile retrieval
-   ✅ Secure logout (token deletion)
-   ✅ Input validation with custom messages
-   ✅ Consistent API responses using ApiResponseTrait
-   ✅ Resource transformation for user data
-   ✅ Error handling with proper HTTP status codes

### Seeded Users

After running the seeders, the following users will be available:

1. **Admin User**

    - Email: `admin@example.com`
    - Password: `password123`

2. **Regular Users**
    - Email: `john@example.com` / Password: `password123`
    - Email: `jane@example.com` / Password: `password123`
    - Email: `bob@example.com` / Password: `password123`
    - Email: `alice@example.com` / Password: `password123`
    - Email: `charlie@example.com` / Password: `password123`

### Database Setup

Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```

### Using the API

1. **Register** a new user or **Login** with existing credentials
2. **Copy the token** from the response
3. **Include the token** in the Authorization header for protected routes:
    ```
    Authorization: Bearer {your_token_here}
    ```
4. **Access protected endpoints** like companies and warehouses
5. **Logout** when done to invalidate the token
