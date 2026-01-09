<?php

namespace Database\Seeders;

use App\Models\Tenants\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::statement('SET FOREIGN_KEY_CHECKS=0');
        // Product::truncate();
        Product::factory()->count(10)->create();
    }
}
