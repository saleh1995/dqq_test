<?php

namespace App\Services;

use App\Enums\StockTransferStatusEnum;
use App\Models\StockTransfer;
use App\Models\StockTransferProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class StockTransferService
{
    /**
     * Get paginated stock transfers with optional filters.
     */
    public function getStockTransfers(array $filters = []): LengthAwarePaginator
    {
        $query = StockTransfer::with([
            'deliveryIntegration',
            'warehouseFrom',
            'warehouseTo',
            'createdBy',
            'products.product'
        ]);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['warehouse_from_id'])) {
            $query->where('warehouse_from_id', $filters['warehouse_from_id']);
        }

        if (isset($filters['warehouse_to_id'])) {
            $query->where('warehouse_to_id', $filters['warehouse_to_id']);
        }

        if (isset($filters['search'])) {
            $query->where(function (Builder $q) use ($filters) {
                $q->where('notes', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('warehouseFrom', function (Builder $q2) use ($filters) {
                        $q2->where('name', 'like', '%' . $filters['search'] . '%');
                    })
                    ->orWhereHas('warehouseTo', function (Builder $q2) use ($filters) {
                        $q2->where('name', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create a new stock transfer.
     */
    public function createStockTransfer(array $data, User $user): StockTransfer
    {
        // dd($data);
        $stockTransfer = StockTransfer::create([
            'delivery_integration_id' => $data['delivery_integration_id'],
            'warehouse_from_id' => $data['warehouse_from_id'],
            'warehouse_to_id' => $data['warehouse_to_id'],
            'status' => StockTransferStatusEnum::NEW ,
            'notes' => $data['notes'] ?? null,
            'created_by' => $user->id,
        ]);

        // Create stock transfer products
        foreach ($data['products'] as $productData) {
            StockTransferProduct::create([
                'stock_transfer_id' => $stockTransfer->id,
                'product_id' => $productData['product_id'],
                'quantity' => $productData['quantity'],
            ]);
        }

        return $stockTransfer->load([
            'deliveryIntegration',
            'warehouseFrom',
            'warehouseTo',
            'createdBy',
            'products.product'
        ]);
    }

    /**
     * Change the status of a stock transfer.
     */
    public function changeStatus(StockTransfer $stockTransfer, StockTransferStatusEnum $newStatus, User $user, ?string $notes = null): bool
    {
        // Check if the transition is allowed
        if (!$stockTransfer->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException('Invalid status transition');
        }

        // Check if the user can perform this action
        if (!$stockTransfer->canUserPerformAction($user, $newStatus)) {
            throw new \InvalidArgumentException('User not authorized to perform this action');
        }

        // Update the status
        $stockTransfer->update([
            'status' => $newStatus,
            'notes' => $notes ?? $stockTransfer->notes,
        ]);

        return true;
    }

    /**
     * Get detailed information about a stock transfer.
     */
    public function getStockTransferDetails(StockTransfer $stockTransfer): StockTransfer
    {
        return $stockTransfer->load([
            'deliveryIntegration',
            'warehouseFrom',
            'warehouseTo',
            'createdBy',
            'products.product',
            'audits'
        ]);
    }

    /**
     * Cancel or return a stock transfer.
     */
    public function cancelOrReturn(StockTransfer $stockTransfer, User $user, ?string $notes = null): bool
    {
        $currentStatus = $stockTransfer->status;

        if (
            in_array($currentStatus, [
                StockTransferStatusEnum::NEW ,
                StockTransferStatusEnum::PREPARING,
                StockTransferStatusEnum::READY
            ])
        ) {
            return $this->changeStatus($stockTransfer, StockTransferStatusEnum::CANCELLED, $user, $notes);
        }

        if (
            in_array($currentStatus, [
                StockTransferStatusEnum::SHIPPING,
                StockTransferStatusEnum::RECEIVED,
                StockTransferStatusEnum::COMPLETED
            ])
        ) {
            return $this->changeStatus($stockTransfer, StockTransferStatusEnum::RETURNING, $user, $notes);
        }

        throw new \InvalidArgumentException('Cannot cancel or return transfer in current status');
    }

    /**
     * Update received quantities for products in a transfer.
     */
    public function updateReceivedQuantities(StockTransfer $stockTransfer, array $receivedData): bool
    {
        if ($stockTransfer->status !== StockTransferStatusEnum::RECEIVED) {
            throw new \InvalidArgumentException('Can only update received quantities for transfers in received status');
        }

        foreach ($receivedData as $data) {
            $product = $stockTransfer->products()->where('product_id', $data['product_id'])->first();

            if ($product) {
                $product->update([
                    'received_quantity' => $data['received_quantity'] ?? 0,
                    'damaged_quantity' => $data['damaged_quantity'] ?? 0,
                ]);
            }
        }

        return true;
    }
}
