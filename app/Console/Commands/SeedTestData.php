<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedTestData extends Command
{
    protected $signature = 'db:seed-test-data';
    protected $description = 'Seed the database with test data for products, warehouses, and stocks';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\ProductsTableSeeder']);
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\WarehousesTableSeeder']);
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\StocksTableSeeder']);

        $this->info('Test data seeded successfully!');
    }
}
