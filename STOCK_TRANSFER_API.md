# Stock Transfer API Documentation

## Overview

The Stock Transfer API provides endpoints to manage stock transfers between warehouses with a comprehensive status workflow system.

## Authentication

All endpoints require authentication using Bearer token. Include the token in the Authorization header:

```
Authorization: Bearer {your-token}
```

## Endpoints

### 1. Get Stock Transfers List

**GET** `/api/stock_transfers`

Get a paginated list of stock transfers with optional filters.

**Query Parameters:**

-   `status` (optional): Filter by status (new, preparing, ready, shipping, received, completed, cancelled, returning)
-   `warehouse_from_id` (optional): Filter by sending warehouse ID
-   `warehouse_to_id` (optional): Filter by receiving warehouse ID
-   `search` (optional): Search in notes and warehouse names
-   `per_page` (optional): Number of items per page (default: 15)

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "delivery_integration": {
                "id": 1,
                "name": "FedEx",
                "type": "delivery"
            },
            "warehouse_from": {
                "id": 1,
                "name": "Main Warehouse",
                "address": "123 Main St"
            },
            "warehouse_to": {
                "id": 2,
                "name": "Branch Warehouse",
                "address": "456 Branch Ave"
            },
            "status": "new",
            "notes": "Test transfer",
            "created_by": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            },
            "products": [
                {
                    "id": 1,
                    "product": {
                        "id": 1,
                        "name": "Product A",
                        "sku": "PROD-001"
                    },
                    "quantity": 10,
                    "received_quantity": null,
                    "damaged_quantity": null
                }
            ],
            "created_at": "2025-08-03T10:00:00.000000Z",
            "updated_at": "2025-08-03T10:00:00.000000Z"
        }
    ],
    "message": "Stock transfers retrieved successfully",
    "status": 200,
    "success": true
}
```

### 2. Get Stock Transfers by Status

**GET** `/api/stock_transfers/statusFilter`

Get stock transfers filtered by a specific status.

**Query Parameters:**

-   `status` (required): Status to filter by
-   `warehouse_from_id` (optional): Filter by sending warehouse ID
-   `warehouse_to_id` (optional): Filter by receiving warehouse ID

**Response:** Same as above

### 3. Create Stock Transfer

**POST** `/api/stock_transfers/store`

Create a new stock transfer.

**Request Body:**

```json
{
    "delivery_integration_id": 1,
    "warehouse_from_id": 1,
    "warehouse_to_id": 2,
    "notes": "Transfer from main to branch",
    "products": [
        {
            "product_id": 1,
            "quantity": 10
        },
        {
            "product_id": 2,
            "quantity": 5
        }
    ]
}
```

**Validation Rules:**

-   `delivery_integration_id`: nullable, exists in companies table
-   `warehouse_from_id`: required, exists in warehouses table
-   `warehouse_to_id`: required, exists in warehouses table, different from warehouse_from_id
-   `notes`: nullable, string, max 1000 characters
-   `products`: required, array, min 1 item
-   `products.*.product_id`: required, exists in products table
-   `products.*.quantity`: required, integer, min 1

**Response:**

```json
{
    "data": {
        "id": 1,
        "status": "new",
        "created_at": "2025-08-03T10:00:00.000000Z"
    },
    "message": "Stock transfer created successfully",
    "status": 201,
    "success": true
}
```

### 4. Change Stock Transfer Status

**POST** `/api/stock_transfers/{id}/change_status`

Change the status of a stock transfer.

**Request Body:**

```json
{
    "status": "preparing",
    "notes": "Starting preparation"
}
```

**Validation Rules:**

-   `status`: required, must be a valid status enum value
-   `notes`: nullable, string, max 1000 characters

**Status Workflow:**
| Current Status | Allowed New Status | Role Required |
|----------------|-------------------|---------------|
| new | preparing, cancelled | Sending warehouse |
| preparing | ready, cancelled | Sending warehouse |
| ready | shipping, cancelled | Sending warehouse |
| shipping | received, returning | Shipping integration (automatic) / Receiving warehouse |
| received | completed, returning | Receiving warehouse |
| completed | returning | Receiving warehouse |

**Response:**

```json
{
    "data": {
        "id": 1,
        "status": "preparing",
        "updated_at": "2025-08-03T10:30:00.000000Z"
    },
    "message": "Stock transfer status updated successfully",
    "status": 200,
    "success": true
}
```

### 5. Get Stock Transfer Details

**GET** `/api/stock_transfers/{id}/info_details`

Get detailed information about a specific stock transfer.

**Response:** Same as the list response but for a single transfer

### 6. Cancel or Return Stock Transfer

**POST** `/api/stock_transfers/{id}/cancel_or_return`

Cancel or return a stock transfer based on its current status.

**Request Body:**

```json
{
    "notes": "Cancelled due to insufficient stock"
}
```

**Logic:**

-   If status is new, preparing, or ready → Cancel (cancelled)
-   If status is shipping, received, or completed → Return (returning)

**Response:**

```json
{
    "data": {
        "id": 1,
        "status": "cancelled",
        "updated_at": "2025-08-03T11:00:00.000000Z"
    },
    "message": "Stock transfer cancelled/returned successfully",
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
        "warehouse_to_id": [
            "The receiving warehouse must be different from the sending warehouse."
        ]
    }
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

### Bad Request (400)

```json
{
    "data": null,
    "message": "Invalid status transition",
    "status": 400,
    "success": false
}
```

### Not Found (404)

```json
{
    "data": null,
    "message": "ERR_004",
    "status": 404,
    "success": false
}
```

## Status Definitions

-   **new**: Transfer has been created but not yet processed
-   **preparing**: Items are being prepared for shipment
-   **ready**: Items are ready for pickup by delivery company
-   **shipping**: Items are in transit
-   **received**: Items have been received at destination warehouse
-   **completed**: Transfer has been fully processed and completed
-   **cancelled**: Transfer has been cancelled (only possible in early stages)
-   **returning**: Items are being returned to origin warehouse

## Authorization Rules

-   **Sending warehouse users** can: create transfers, change status to preparing/ready/shipping, cancel transfers
-   **Receiving warehouse users** can: change status to completed, return transfers
-   **Delivery integration** can: automatically change status from shipping to received
-   Users must be associated with the appropriate warehouse to perform actions
