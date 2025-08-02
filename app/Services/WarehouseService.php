<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class WarehouseService
{
    /**
     * Get all warehouses with optional pagination
     */
    public function getAllWarehouses(int $perPage = 10): LengthAwarePaginator
    {
        return Warehouse::paginate($perPage);
    }

    /**
     * Get a single warehouse by ID
     */
    public function getWarehouseById(int $id): ?Warehouse
    {
        return Warehouse::find($id);
    }

    /**
     * Create a new warehouse
     */
    public function createWarehouse(array $data): Warehouse
    {
        return Warehouse::create($data);
    }

    /**
     * Update an existing warehouse
     */
    public function updateWarehouse(int $id, array $data): ?Warehouse
    {
        $warehouse = Warehouse::find($id);

        if ($warehouse) {
            $warehouse->update($data);
            return $warehouse->fresh();
        }

        return null;
    }

    /**
     * Delete a warehouse
     */
    public function deleteWarehouse(int $id): bool
    {
        $warehouse = Warehouse::find($id);

        if ($warehouse) {
            return $warehouse->delete();
        }

        return false;
    }

    /**
     * Check if warehouse exists
     */
    public function warehouseExists(int $id): bool
    {
        return Warehouse::where('id', $id)->exists();
    }
}
