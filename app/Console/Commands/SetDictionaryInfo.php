<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetDictionaryInfo extends Command
{
    protected $signature = 'sync:dict_data';
    protected $description = 'Для заполнения справочников ERP МойСклад';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->call('sync:moysklad', ['type' => 'status']);
        $this->call('sync:moysklad', ['type' => 'counterparty_status']);
        $this->call('sync:moysklad', ['type' => 'regions']);

        $this->info('Non periodic data is loaded');
    }
}
