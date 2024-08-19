<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse;

class StocksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = Warehouse::query()
            ->addSelect('id')
            ->whereIn('name', ['Гамбург', 'Берлин', 'Остин'])->get();

        $warehouse = [];

        foreach ($data as $item) {
            $warehouse[] = $item['id'];
        }

        $data = Product::query()
            ->addSelect('id')
            ->whereIn('name', ['Product 1', 'Product 2', 'Product 3'])->get();

        $product = [];

        foreach ($data as $item) {
            $product[] = $item['id'];
        }

        $stocks = [];

        for ($i = 0; $i < 100; $i++) {
            $stocks[] = [
                'product_id' => $product[random_int(0, 2)],
                'warehouse_id' => $warehouse[random_int(0, 2)],
                'stock' => random_int(10, 200),
                'created_at' => new \DateTime(),
            ];
        }

        DB::table('stocks')->insert($stocks);
    }
}
