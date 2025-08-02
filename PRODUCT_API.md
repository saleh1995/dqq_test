# Products API Documentation

This document describes the API endpoints for managing products in the system.

## Base URL

```
http://localhost:8000/api
```

## Authentication

All endpoints require authentication using Laravel Sanctum. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {your_token}
```

## Endpoints

### 1. Get All Products

**GET** `/products`

Retrieves a paginated list of all products.

**Query Parameters:**

-   `per_page` (optional): Number of products per page (default: 10)

**Response:**

```json
{
    "data": {
        "data": [
            {
                "id": 1,
                "name": "Laptop",
                "created_at": "2025-08-02T16:56:01.000000Z",
                "updated_at": "2025-08-02T16:56:01.000000Z"
            }
        ],
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1
    },
    "message": "Products retrieved successfully",
    "status_code": 200,
    "success": true
}
```

### 2. Create Product

**POST** `/products`

Creates a new product.

**Request Body:**

```json
{
    "name": "New Product"
}
```

**Validation Rules:**

-   `name`: required, string, max 255 characters

**Response:**

```json
{
    "data": {
        "id": 2,
        "name": "New Product",
        "created_at": "2025-08-02T16:56:01.000000Z",
        "updated_at": "2025-08-02T16:56:01.000000Z"
    },
    "message": "Product created successfully",
    "status_code": 201,
    "success": true
}
```

**Error Response (Duplicate Name):**

```json
{
    "data": null,
    "message": "A product with this name already exists",
    "status_code": 422,
    "success": false
}
```

### 3. Get Single Product

**GET** `/products/{id}`

Retrieves a specific product by ID.

**Response:**

```json
{
    "data": {
        "id": 1,
        "name": "Laptop",
        "created_at": "2025-08-02T16:56:01.000000Z",
        "updated_at": "2025-08-02T16:56:01.000000Z"
    },
    "message": "Product retrieved successfully",
    "status_code": 200,
    "success": true
}
```

**Error Response (Not Found):**

```json
{
    "data": null,
    "message": "Product not found",
    "status_code": 404,
    "success": false
}
```

### 4. Update Product

**PUT/PATCH** `/products/{id}`

Updates an existing product.

**Request Body:**

```json
{
    "name": "Updated Product Name"
}
```

**Validation Rules:**

-   `name`: required, string, max 255 characters

**Response:**

```json
{
    "data": {
        "id": 1,
        "name": "Updated Product Name",
        "created_at": "2025-08-02T16:56:01.000000Z",
        "updated_at": "2025-08-02T16:56:01.000000Z"
    },
    "message": "Product updated successfully",
    "status_code": 200,
    "success": true
}
```

**Error Response (Duplicate Name):**

```json
{
    "data": null,
    "message": "A product with this name already exists",
    "status_code": 422,
    "success": false
}
```

### 5. Delete Product

**DELETE** `/products/{id}`

Deletes a product.

**Response:**

```json
{
    "data": null,
    "message": "Product deleted successfully",
    "status_code": 200,
    "success": true
}
```

**Error Response (Not Found):**

```json
{
    "data": null,
    "message": "Product not found",
    "status_code": 404,
    "success": false
}
```

## Error Responses

### Validation Error

```json
{
    "data": null,
    "message": "The given data was invalid.",
    "errors": {
        "name": ["The name field is required."]
    },
    "status_code": 422,
    "success": false
}
```

### Server Error

```json
{
    "data": null,
    "message": "Internal server error",
    "status_code": 500,
    "success": false
}
```

## Example Usage with cURL

### Get All Products

```bash
curl -X GET "http://localhost:8000/api/products" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json"
```

### Create Product

```bash
curl -X POST "http://localhost:8000/api/products" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name": "New Product"}'
```

### Update Product

```bash
curl -X PUT "http://localhost:8000/api/products/1" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name": "Updated Product"}'
```

### Delete Product

```bash
curl -X DELETE "http://localhost:8000/api/products/1" \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json"
```
