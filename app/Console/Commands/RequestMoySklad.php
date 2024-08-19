<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RequestMoySklad extends Command
{
    private const BASE_URL = 'https://api.moysklad.ru/api/remap/1.2';

    private int $limit = 1000;

    private int $offset = 0;

    private string $privateKey;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:moysklad {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Actual MoySklad Data Set';

    public function __construct()
    {
        $this->privateKey = config('app.moy_sklad');

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->argument('type')) {
            $this->error('Не передан обязательный параметр type, пример: catalog');

            return Command::FAILURE;
        }

        $path = match ($this->argument('type')) {
            'catalog' => '/entity/assortment',
            default => throw new RuntimeException('Parameter type is mandatory, input type is not support!'),
        };

        $http = Http::withHeaders(
            [
                'Authorization' => sprintf('Bearer %s', $this->privateKey),
                'Accept-Encoding' => 'gzip',
            ],
        );

        //  curl -X GET
        //  "https://api.moysklad.ru/api/remap/1.2/entity/assortment"
        //  -H "Authorization: Bearer <Credentials>"
        //  -H "Accept-Encoding: gzip"

        $response = $http->get(
            $this->getUri($path),
            [
                'limit' => $this->limit,
                'offset' => $this->offset,
            ],
        );

        Log::info(print_r($response->json(), true));

        $this->info('Data set synchronized successfully!');

        return Command::SUCCESS;
    }

    private function getUri(string $path): string
    {
        return static::BASE_URL . $path;
    }
}
