<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            ['name' => 'Product 1', 'price' => 10.00, 'created_at' => new \DateTime()],
            ['name' => 'Product 2', 'price' => 20.00, 'created_at' => new \DateTime()],
            ['name' => 'Product 3', 'price' => 30.00, 'created_at' => new \DateTime()],
        ]);
    }
}

