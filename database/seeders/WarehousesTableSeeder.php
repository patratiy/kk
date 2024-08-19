<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehousesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('warehouses')->insert([
            ['name' => 'Гамбург', 'created_at' => new \DateTime()],
            ['name' => 'Берлин', 'created_at' => new \DateTime()],
            ['name' => 'Остин', 'created_at' => new \DateTime()],
        ]);
    }
}

