# Warehouse API Documentation

This document describes the Warehouse CRUD API endpoints implemented using Laravel with service classes and the ApiResponseTrait.

## Base URL

```
/api/warehouses
```

## Endpoints

### 1. Get All Warehouses

**GET** `/api/warehouses`

**Query Parameters:**

-   `per_page` (optional): Number of items per page (default: 10)

**Response:**

```json
{
    "data": {
        "current_page": 1,
        "data": [],
        "first_page_url": "http://127.0.0.1:8000/api/warehouses?page=1",
        "from": null,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/warehouses?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/warehouses?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "next_page_url": null,
        "path": "http://127.0.0.1:8000/api/warehouses",
        "per_page": 10,
        "prev_page_url": null,
        "to": null,
        "total": 0
    }
}
```

### 2. Get Single Warehouse

**GET** `/api/warehouses/{id}`

**Response:**

```json
{
    "data": {
        "id": 1,
        "name": "Main Warehouse",
        "created_at": "2025-08-02T14:00:00.000000Z",
        "updated_at": "2025-08-02T14:00:00.000000Z"
    },
    "message": "Warehouse retrieved successfully",
    "status": 200,
    "success": true
}
```

### 3. Create Warehouse

**POST** `/api/warehouses`

**Request Body:**

```json
{
    "name": "New Warehouse"
}
```

**Response:**

```json
{
    "data": {
        "id": 2,
        "name": "New Warehouse",
        "created_at": "2025-08-02T14:00:00.000000Z",
        "updated_at": "2025-08-02T14:00:00.000000Z"
    },
    "message": "Warehouse created successfully",
    "status": 201,
    "success": true
}
```

### 4. Update Warehouse

**PUT** `/api/warehouses/{id}`

**Request Body:**

```json
{
    "name": "Updated Warehouse"
}
```

**Response:**

```json
{
    "data": {
        "id": 1,
        "name": "Updated Warehouse",
        "created_at": "2025-08-02T14:00:00.000000Z",
        "updated_at": "2025-08-02T14:00:00.000000Z"
    },
    "message": "Warehouse updated successfully",
    "status": 200,
    "success": true
}
```

### 5. Delete Warehouse

**DELETE** `/api/warehouses/{id}`

**Response:**

```json
{
    "data": null,
    "message": "Warehouse deleted successfully",
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
        "name": ["The warehouse name field is required."]
    }
}
```

### Not Found Error (404)

```json
{
    "data": null,
    "message": "Warehouse not found",
    "status": 404,
    "success": false
}
```

### Duplicate Name Error (422)

```json
{
    "data": null,
    "message": "A warehouse with this name already exists",
    "status": 422,
    "success": false
}
```

## Authentication

All warehouse endpoints require authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your_token_here}
```

## Implementation Details

### Architecture

-   **Controller**: `App\Http\Controllers\Api\WarehouseController`
-   **Service**: `App\Services\WarehouseService`
-   **Model**: `App\Models\Warehouse`
-   **Resource**: `App\Http\Resources\WarehouseResource`
-   **Request Validation**: `App\Http\Requests\WarehouseRequest`
-   **Response Trait**: `App\Traits\ApiResponseTrait`

### Features

-   ✅ Full CRUD operations
-   ✅ Pagination support
-   ✅ Input validation
-   ✅ Consistent API responses using ApiResponseTrait
-   ✅ Resource transformation for consistent data format
-   ✅ Error handling with proper HTTP status codes
-   ✅ Duplicate name prevention
-   ✅ Service layer for business logic separation

### Database Setup

Run migrations:

```bash
php artisan migrate
```
