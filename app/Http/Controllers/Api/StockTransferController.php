<?php

namespace App\Http\Controllers\Api;

use App\Enums\StockTransferStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\StockTransferRequest;
use App\Http\Resources\StockTransferResource;
use App\Models\StockTransfer;
use App\Services\StockTransferService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockTransferController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private StockTransferService $stockTransferService
    ) {
    }

    /**
     * Get paginated list of stock transfers with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'status',
            'warehouse_from_id',
            'warehouse_to_id',
            'search',
            'per_page'
        ]);

        $stockTransfers = $this->stockTransferService->getStockTransfers($filters);

        return $this->apiResponse(
            StockTransferResource::collection($stockTransfers),
            'Stock transfers retrieved successfully',
            200,
            true
        );
    }

    /**
     * Get stock transfers filtered by status.
     */
    public function statusFilter(Request $request): JsonResponse
    {
        $status = $request->get('status');

        if (!$status) {
            return $this->apiResponseError(null, 'Status parameter is required', 400);
        }

        $filters = ['status' => $status];

        if ($request->has('warehouse_from_id')) {
            $filters['warehouse_from_id'] = $request->get('warehouse_from_id');
        }

        if ($request->has('warehouse_to_id')) {
            $filters['warehouse_to_id'] = $request->get('warehouse_to_id');
        }

        $stockTransfers = $this->stockTransferService->getStockTransfers($filters);

        return $this->apiResponse(
            StockTransferResource::collection($stockTransfers),
            'Stock transfers filtered by status successfully',
            200,
            true
        );
    }

    /**
     * Create a new stock transfer.
     */
    public function store(StockTransferRequest $request): JsonResponse
    {
        try {
            $stockTransfer = $this->stockTransferService->createStockTransfer(
                $request->validated(),
                $request->user()
            );

            return $this->apiResponse(
                new StockTransferResource($stockTransfer),
                'Stock transfer created successfully',
                201,
                true
            );
        } catch (\Exception $e) {
            return $this->apiResponseError(null, 'Failed to create stock transfer: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Change the status of a stock transfer.
     */
    public function changeStatus(ChangeStatusRequest $request, StockTransfer $stockTransfer): JsonResponse
    {
        try {
            $this->stockTransferService->changeStatus(
                $stockTransfer,
                StockTransferStatusEnum::from($request->validated()['status']),
                $request->user(),
                $request->validated()['notes'] ?? null
            );

            $updatedTransfer = $this->stockTransferService->getStockTransferDetails($stockTransfer);

            return $this->apiResponse(
                new StockTransferResource($updatedTransfer),
                'Stock transfer status updated successfully',
                200,
                true
            );
        } catch (\InvalidArgumentException $e) {
            return $this->apiResponseError(null, $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->apiResponseError(null, 'Failed to update stock transfer status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get detailed information about a stock transfer.
     */
    public function infoDetails(StockTransfer $stockTransfer): JsonResponse
    {
        $stockTransfer = $this->stockTransferService->getStockTransferDetails($stockTransfer);

        return $this->apiResponse(
            new StockTransferResource($stockTransfer),
            'Stock transfer details retrieved successfully',
            200,
            true
        );
    }

    /**
     * Cancel or return a stock transfer.
     */
    public function cancelOrReturn(Request $request, StockTransfer $stockTransfer): JsonResponse
    {
        try {
            $this->stockTransferService->cancelOrReturn(
                $stockTransfer,
                $request->user(),
                $request->get('notes')
            );

            $updatedTransfer = $this->stockTransferService->getStockTransferDetails($stockTransfer);

            return $this->apiResponse(
                new StockTransferResource($updatedTransfer),
                'Stock transfer cancelled/returned successfully',
                200,
                true
            );
        } catch (\InvalidArgumentException $e) {
            return $this->apiResponseError(null, $e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->apiResponseError(null, 'Failed to cancel/return stock transfer: ' . $e->getMessage(), 500);
        }
    }
}
