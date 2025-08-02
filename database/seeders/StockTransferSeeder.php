<?php

namespace Database\Seeders;

use App\Enums\StockTransferStatusEnum;
use App\Models\Company;
use App\Models\Product;
use App\Models\StockTransfer;
use App\Models\StockTransferProduct;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $warehouses = Warehouse::all();
        $products = Product::all();
        $users = User::whereNotNull('warehouse_id')->get(); // Only users with warehouse assignments
        $deliveryCompanies = Company::all();

        if ($warehouses->count() < 2 || $products->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Skipping StockTransferSeeder: Insufficient data');
            return;
        }

        $statuses = [
            StockTransferStatusEnum::NEW ,
            StockTransferStatusEnum::PREPARING,
            StockTransferStatusEnum::READY,
            StockTransferStatusEnum::SHIPPING,
            StockTransferStatusEnum::RECEIVED,
            StockTransferStatusEnum::COMPLETED,
        ];

        for ($i = 0; $i < 10; $i++) {
            $warehouseFrom = $warehouses->random();
            $warehouseTo = $warehouses->where('id', '!=', $warehouseFrom->id)->random();
            $user = $users->random();
            $deliveryCompany = $deliveryCompanies->random();
            $status = $statuses[array_rand($statuses)];

            $stockTransfer = StockTransfer::create([
                'delivery_integration_id' => $deliveryCompany->id,
                'warehouse_from_id' => $warehouseFrom->id,
                'warehouse_to_id' => $warehouseTo->id,
                'status' => $status,
                'notes' => "Test transfer #" . ($i + 1),
                'created_by' => $user->id,
            ]);

            // Add 1-3 products to each transfer
            $transferProducts = $products->random(rand(1, 3));
            foreach ($transferProducts as $product) {
                StockTransferProduct::create([
                    'stock_transfer_id' => $stockTransfer->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 10),
                    'received_quantity' => in_array($status, [StockTransferStatusEnum::RECEIVED, StockTransferStatusEnum::COMPLETED]) ? rand(1, 10) : null,
                    'damaged_quantity' => in_array($status, [StockTransferStatusEnum::RECEIVED, StockTransferStatusEnum::COMPLETED]) ? rand(0, 2) : null,
                ]);
            }
        }

        $this->command->info('Stock transfers seeded successfully!');
    }
}
