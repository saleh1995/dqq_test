# Company API Documentation

This document describes the Company CRUD API endpoints implemented using Laravel with service classes and the ApiResponseTrait.

## Base URL

```
/api/companies
```

## Endpoints

### 1. Get All Companies

**GET** `/api/companies`

**Query Parameters:**

-   `per_page` (optional): Number of items per page (default: 10)

**Response:**

```json
{
    "data": {
        "current_page": 1,
        "data": [],
        "first_page_url": "http://127.0.0.1:8000/api/companies?page=1",
        "from": null,
        "last_page": 1,
        "last_page_url": "http://127.0.0.1:8000/api/companies?page=1",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/companies?page=1",
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
        "path": "http://127.0.0.1:8000/api/companies",
        "per_page": 10,
        "prev_page_url": null,
        "to": null,
        "total": 0
    },
    "message": "Companies retrieved successfully",
    "status": 200,
    "success": true
}
```

### 2. Get Single Company

**GET** `/api/companies/{id}`

**Response:**

```json
{
    "data": {
        "id": 1,
        "name": "Example Company",
        "created_at": "2025-08-02T15:00:00.000000Z",
        "updated_at": "2025-08-02T15:00:00.000000Z"
    },
    "message": "Company retrieved successfully",
    "status": 200,
    "success": true
}
```

### 3. Create Company

**POST** `/api/companies`

**Request Body:**

```json
{
    "name": "New Company"
}
```

**Response:**

```json
{
    "data": {
        "id": 2,
        "name": "New Company",
        "created_at": "2025-08-02T15:00:00.000000Z",
        "updated_at": "2025-08-02T15:00:00.000000Z"
    },
    "message": "Company created successfully",
    "status": 201,
    "success": true
}
```

### 4. Update Company

**PUT** `/api/companies/{id}`

**Request Body:**

```json
{
    "name": "Updated Company"
}
```

**Response:**

```json
{
    "data": {
        "id": 1,
        "name": "Updated Company",
        "created_at": "2025-08-02T15:00:00.000000Z",
        "updated_at": "2025-08-02T15:00:00.000000Z"
    },
    "message": "Company updated successfully",
    "status": 200,
    "success": true
}
```

### 5. Delete Company

**DELETE** `/api/companies/{id}`

**Response:**

```json
{
    "data": null,
    "message": "Company deleted successfully",
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
        "name": ["The company name field is required."]
    }
}
```

### Not Found Error (404)

```json
{
    "data": null,
    "message": "Company not found",
    "status": 404,
    "success": false
}
```

### Duplicate Name Error (422)

```json
{
    "data": null,
    "message": "A company with this name already exists",
    "status": 422,
    "success": false
}
```

## Implementation Details

### Architecture

-   **Controller**: `App\Http\Controllers\Api\CompanyController`
-   **Service**: `App\Services\CompanyService`
-   **Model**: `App\Models\Company`
-   **Resource**: `App\Http\Resources\CompanyResource`
-   **Request Validation**: `App\Http\Requests\CompanyRequest`
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
