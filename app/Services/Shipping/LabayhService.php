<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Shipment;
use Illuminate\Support\Facades\Log;

class LabayhService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.labayh.token', 'ea90b1a2c6cbace9cc92172356bd90149a5cb7e7');
        $this->baseUrl = config('services.labayh.base_url', 'https://dev.mylabaih.com/');
    }

    public function createShipment(array $payload): Shipment
    {
        $payload['api_key'] = $this->apiKey;

        $response = Http::asForm()->post($this->baseUrl . 'partners/api/order/create', $payload);

        $data = $response->json();

        $shipment = Shipment::create([
            'order_number' => $payload['customerOrderNo'],
            'shipment_reference' => $data['consignmentNo'] ?? null,
            'status' => $data['status'] ?? 'created',
            'raw_response' => $data,
        ]);

        // Download and upload label
        if (!empty($shipment->shipment_reference)) {
            $label = Http::get($this->baseUrl . 'partners/api/order/printlabel', [
                'api_key' => $this->apiKey,
                'consignmentNo' => $shipment->shipment_reference,
            ]);

            $path = 'labels/labayh-' . $shipment->shipment_reference . '.pdf';
            Storage::disk('s3')->put($path, $label->body());
            $shipment->label_url = Storage::disk('s3')->url($path);
            $shipment->save();
        }

        return $shipment;
    }

    public function trackShipment(string $consignmentNo): array
    {
        $response = Http::get($this->baseUrl . 'partners/api/order/track', [
            'api_key' => $this->apiKey,
            'consignmentNo' => $consignmentNo,
        ]);

        return $response->json();
    }

    public function returnShipment(string $consignmentNo): array
    {
        $response = Http::asForm()->post($this->baseUrl . 'partners/api/order/return', [
            'api_key' => $this->apiKey,
            'consignmentNo' => $consignmentNo,
        ]);

        return $response->json();
    }
}
