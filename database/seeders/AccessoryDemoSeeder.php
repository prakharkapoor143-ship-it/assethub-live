<?php

namespace Database\Seeders;

use App\Models\Accessory;
use Illuminate\Database\Seeder;

final class AccessoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        Accessory::query()->firstOrCreate(
            ['sku' => 'ACC-1001'],
            [
                'name' => 'USB-C Dock',
                'model_number' => 'DK-22',
                'quantity' => 20,
                'checked_out' => 5,
                'min_quantity' => 3,
            ]
        );
    }
}
