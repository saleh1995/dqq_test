<?php

namespace App\Listeners;

use App\Events\CreateShipmentEvent;
use App\Services\Shipping\LabayhService;
use App\Services\StockTransferService;
use App\Services\Routing\RoutingEngine;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateShipmentListener
{
    public function __construct(
        private StockTransferService $stockTransferService,
        private LabayhService $labayhService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(CreateShipmentEvent $event): void
    {
        $stockTransfer = $event->stockTransfer;
        $stockTransferDetails = $this->stockTransferService->getStockTransferDetails($stockTransfer);
        $data = [
            'consignee_name' => $stockTransferDetails->warehouseTo->name,
            'consignee_mobile' => $stockTransferDetails->warehouseTo->mobile,
            'consignee_city' => $stockTransferDetails->warehouseTo->city,
            'consignee_address' => $stockTransferDetails->warehouseTo->address,
            'consignee_lat_lng' => $stockTransferDetails->warehouseTo->lat . ',' . $stockTransferDetails->warehouseTo->lng,
        ];

        $provider = RoutingEngine::decideProvider($data);

        if ($provider === 'labayh') {
            RoutingEngine::log(
                $stockTransfer->id,
                'labayh',
                $data,
                'Auto-selected Labayh for delivery integration'
            );

            $shipmentPayload = [
                'customerOrderNo' => $stockTransfer->id,
                'consigneeName' => $data['consignee_name'],
                'consigneeMobile' => $data['consignee_mobile'],
                'consigneeCity' => $data['consignee_city'],
                'consigneeAddress' => $data['consignee_address'],
                'consigneeLatLong' => $data['consignee_lat_lng'],
                'store' => 'LaBaih',
                'crates' => count($data['products']),
                'weightKg' => $data['weight'] ?? 5,
                'special_instructions' => $data['special_instructions'] ?? '',
            ];

            $shipment = $this->labayhService->createShipment($shipmentPayload);

            $stockTransfer->update([
                'tracking_number' => $shipment->shipment_reference,
            ]);
        }

    }
}
