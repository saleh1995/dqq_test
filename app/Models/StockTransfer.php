<?php

namespace App\Models;

use App\Enums\StockTransferStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_integration_id',
        'warehouse_from_id',
        'warehouse_to_id',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'status' => StockTransferStatusEnum::class,
    ];

    /**
     * Get the delivery company for this transfer.
     */
    public function deliveryIntegration(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'delivery_integration_id');
    }

    /**
     * Get the sending warehouse.
     */
    public function warehouseFrom(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_from_id');
    }

    /**
     * Get the receiving warehouse.
     */
    public function warehouseTo(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_to_id');
    }

    /**
     * Get the user who created this transfer.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the products associated with this transfer.
     */
    public function products(): HasMany
    {
        return $this->hasMany(StockTransferProduct::class);
    }

    /**
     * Check if the current status allows a specific action.
     */
    public function canTransitionTo(StockTransferStatusEnum $newStatus): bool
    {
        $allowedTransitions = [
            StockTransferStatusEnum::NEW->value => [
                StockTransferStatusEnum::PREPARING->value,
                StockTransferStatusEnum::CANCELLED->value,
            ],
            StockTransferStatusEnum::PREPARING->value => [
                StockTransferStatusEnum::READY->value,
                StockTransferStatusEnum::CANCELLED->value,
            ],
            StockTransferStatusEnum::READY->value => [
                StockTransferStatusEnum::SHIPPING->value,
                StockTransferStatusEnum::CANCELLED->value,
            ],
            StockTransferStatusEnum::SHIPPING->value => [
                StockTransferStatusEnum::RECEIVED->value,
                StockTransferStatusEnum::RETURNING->value,
            ],
            StockTransferStatusEnum::RECEIVED->value => [
                StockTransferStatusEnum::COMPLETED->value,
                StockTransferStatusEnum::RETURNING->value,
            ],
            StockTransferStatusEnum::COMPLETED->value => [
                StockTransferStatusEnum::RETURNING->value,
            ],
        ];

        return in_array($newStatus->value, $allowedTransitions[$this->status->value] ?? []);
    }

    /**
     * Check if a user can perform an action based on their role and warehouse.
     */
    public function canUserPerformAction(User $user, StockTransferStatusEnum $newStatus): bool
    {
        if ($newStatus === StockTransferStatusEnum::CANCELLED) {
            // Only sending warehouse can cancel
            return $user->warehouse_id === $this->warehouse_from_id;
        }

        if ($newStatus === StockTransferStatusEnum::RETURNING) {
            // Only receiving warehouse can return
            return $user->warehouse_id === $this->warehouse_to_id;
        }

        if (
            in_array($newStatus, [
                StockTransferStatusEnum::PREPARING,
                StockTransferStatusEnum::READY,
                StockTransferStatusEnum::SHIPPING,
            ])
        ) {
            // Only sending warehouse can perform these actions
            return $user->warehouse_id === $this->warehouse_from_id;
        }

        if ($newStatus === StockTransferStatusEnum::COMPLETED) {
            // Only receiving warehouse can complete
            return $user->warehouse_id === $this->warehouse_to_id;
        }

        return false;
    }
}
