<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            'Main Distribution Center',
            'North Regional Warehouse',
            'South Storage Facility',
            'East Logistics Hub',
            'West Supply Depot',
            'Central Inventory Center',
            'Coastal Shipping Terminal',
            'Mountain Storage Complex',
            'Urban Retail Warehouse',
            'Industrial Manufacturing Storage',
        ];

        foreach ($warehouses as $warehouseName) {
            Warehouse::updateOrCreate(
                ['name' => $warehouseName],
                ['name' => $warehouseName]
            );
        }
    }
}
