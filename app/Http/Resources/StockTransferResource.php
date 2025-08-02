<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'delivery_integration' => $this->whenLoaded('deliveryIntegration', function () {
                return new CompanyResource($this->deliveryIntegration);
            }),
            'warehouse_from' => $this->whenLoaded('warehouseFrom', function () {
                return new WarehouseResource($this->warehouseFrom);
            }),
            'warehouse_to' => $this->whenLoaded('warehouseTo', function () {
                return new WarehouseResource($this->warehouseTo);
            }),
            'status' => $this->status->value,
            'notes' => $this->notes,
            'created_by' => $this->whenLoaded('createdBy', function () {
                return new UserResource($this->createdBy);
            }),
            'products' => StockTransferProductResource::collection($this->whenLoaded('products')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'audits' => $this->whenLoaded('audits', function(){
                return $this->audits;
            })
        ];
    }
}
