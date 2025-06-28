<?php

namespace Database\Seeders;

use App\Models\GiftPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GiftPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Start', 'quantity' => 10, 'price_usd' => 1],
            ['name' => 'Booster', 'quantity' => 33, 'price_usd' => 3],
            ['name' => 'Power', 'quantity' => 55, 'price_usd' => 5],
            ['name' => 'Pro', 'quantity' => 110, 'price_usd' => 10],
            ['name' => 'Ultra', 'quantity' => 230, 'price_usd' => 20],
            ['name' => 'Legend', 'quantity' => 550, 'price_usd' => 50],
        ];

        foreach ($data as $package) {
            GiftPackage::create($package);
        }
    }
}
