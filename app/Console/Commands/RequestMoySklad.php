<?php

namespace App\Console\Commands;

use App\Models\Prices;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class RequestMoySklad extends Command
{
    private int $limit = 1000;

    private int $offset = 0;

    private string $privateKey;
    private static string $baseUrl;

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
        static::$baseUrl = config('app.base_url_api_moy_sklad');

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

        $start = Carbon::now()->getTimestamp();

        $response = $http->get(
            static::getUri($path),
            [
                'limit' => $this->limit,
                'offset' => $this->offset,
            ],
        );

        while ($response['meta']['size'] > $this->offset) {
            match ($this->argument('type')) {
                'catalog' => static::processCatalogData($response->json()),
                default => throw new RuntimeException('Parameter type is mandatory, input type is not support!'),
            };

            $this->offset += $this->limit;

            $response = $http->get(
                static::getUri($path),
                [
                    'limit' => $this->limit,
                    'offset' => $this->offset,
                ],
            );
        }

        Log::info(
            'Time spend on execution sync', [
                'type' => $this->argument('type'),
                'sec' => Carbon::now()->getTimestamp() - $start,
            ],
        );

//        Log::info(print_r($response->json(), true));

        $this->info('Data set synchronized successfully!');

        return Command::SUCCESS;
    }

    private static function getUri(string $path): string
    {
        return sprintf('%s%s', static::$baseUrl, $path);
    }

    private static function extractGuidFromUri(string $uri): string
    {
        return last(explode('/', $uri));
    }

    private static function processCatalogData(array $data): void
    {
        if (empty($data['rows'])) {
            return;
        }

        foreach ($data['rows'] as $item) {
            $brand = current(
                array_filter(
                    $item['attributes'],
                    static function (mixed $item) {
                        return $item['id'] === '0076b518-d9b3-11eb-0a80-06ae0011d1aa';
                    },
                ),
            );

            $folderId = $item['productFolder']
                ? static::extractGuidFromUri($item['productFolder']['meta']['href'])
                : '';

            $supplierId = $item['supplier']
                ? static::extractGuidFromUri($item['supplier']['meta']['href'])
                : '';

            $bcodes = [];

            foreach ($item['barcodes'] as $bcode) {
                $key = current(array_keys($bcode));
                $value = current(array_values($bcode));
                $bcodes[$key] = $value;
            }

            Product::query()->updateOrInsert(
                [
                    'ext_id' => $item['id'],
                ],
                [
                    'name' => $item['name'],
                    'group_id' => $folderId,
                    'code' => $item['code'],
                    'ext_code' => $item['externalCode'],
                    'article' => $item['article'],
                    'buy_price' => $item['buyPrice']['value'] / 100,
                    'ean13' => $bcodes['ean13'] ?? '',
                    'gtin' => $bcodes['gtin'] ?? '',
                    'group_name' => $item['pathName'] ?? '',
                    'supplier_id' => $supplierId,
                    'has_images' => $item['images']['meta']['size'] > 0,
                    'brand' => $brand['value']['name'],
                    'stock' => $item['stock'],
                    'reserve' => $item['reserve'],
                    'quantity' => $item['quantity'],
                    'updated_at' => Carbon::parse($item['updated']),
                ],
            );

            array_map(
                static function (mixed $item) {
                    Prices::query()->updateOrInsert(
                        [
                            'product_id' => $item['id'],
                        ],
                        [
                            'type_name' => $item['priceType']['name'],
                            'type_id' => $item['priceType']['id'],
                            'value' => $item['value'] / 100,
                        ],
                    );
                },
                $item['salePrices'],
            );
        }
    }
}
