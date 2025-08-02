<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Traits\ApiResponseTrait;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WarehouseController extends Controller
{
    use ApiResponseTrait;

    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $warehouses = $this->warehouseService->getAllWarehouses($perPage);

            return $this->apiResponse(
                WarehouseResource::collection($warehouses)->resource,
                'Warehouses retrieved successfully',
                200,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WarehouseRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Check if warehouse with same name already exists
            if (Warehouse::where('name', $validatedData['name'])->exists()) {
                return $this->apiResponseError(
                    null,
                    'A warehouse with this name already exists',
                    422
                );
            }

            $warehouse = $this->warehouseService->createWarehouse($validatedData);

            return $this->apiResponse(
                new WarehouseResource($warehouse),
                'Warehouse created successfully',
                201,
                true
            );
        } catch (ValidationException $e) {
            return $this->apiResponseException($e);
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $warehouse = $this->warehouseService->getWarehouseById($id);

            if (!$warehouse) {
                return $this->apiResponseError(
                    null,
                    'Warehouse not found',
                    404
                );
            }

            return $this->apiResponse(
                new WarehouseResource($warehouse),
                'Warehouse retrieved successfully',
                200,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WarehouseRequest $request, string $id)
    {
        try {
            // Check if warehouse exists
            if (!$this->warehouseService->warehouseExists($id)) {
                return $this->apiResponseError(
                    null,
                    'Warehouse not found',
                    404
                );
            }

            $validatedData = $request->validated();

            // Check if warehouse with same name already exists (excluding current warehouse)
            if (Warehouse::where('name', $validatedData['name'])->where('id', '!=', $id)->exists()) {
                return $this->apiResponseError(
                    null,
                    'A warehouse with this name already exists',
                    422
                );
            }

            $warehouse = $this->warehouseService->updateWarehouse($id, $validatedData);

            return $this->apiResponse(
                new WarehouseResource($warehouse),
                'Warehouse updated successfully',
                200,
                true
            );
        } catch (ValidationException $e) {
            return $this->apiResponseException($e);
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Check if warehouse exists
            if (!$this->warehouseService->warehouseExists($id)) {
                return $this->apiResponseError(
                    null,
                    'Warehouse not found',
                    404
                );
            }

            $deleted = $this->warehouseService->deleteWarehouse($id);

            if ($deleted) {
                return $this->apiResponse(
                    null,
                    'Warehouse deleted successfully',
                    200,
                    true
                );
            } else {
                return $this->apiResponseError(
                    null,
                    'Failed to delete warehouse',
                    500
                );
            }
        } catch (\Exception $e) {
            return $this->apiResponseException($e);
        }
    }
}
