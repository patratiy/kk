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

    private static function getValueAttributeById(
        array $params,
        string $attrId,
    ): string
    {
        $item = array_filter(
            $params['attributes'] ?? [],
            static function (mixed $item) use ($attrId) {
                return $item['id'] === $attrId;
            },
        );

        return current($item)['value']['name'] ?? '';
    }

    private static function processCatalogData(array $data): void
    {
        if (empty($data['rows'])) {
            return;
        }

        foreach ($data['rows'] as $product) {
            $brand = static::getValueAttributeById(
                params: $product,
                attrId: '0076b518-d9b3-11eb-0a80-06ae0011d1aa',
            );

            $status = static::getValueAttributeById(
                params: $product,
                attrId: '79075eb4-5557-11ef-0a80-07340020fca4',
            );

            $folderId = isset($product['productFolder'])
                ? static::extractGuidFromUri($product['productFolder']['meta']['href'])
                : '';

            $supplierId = isset($product['supplier'])
                ? static::extractGuidFromUri($product['supplier']['meta']['href'])
                : '';

            $bcodes = [];

            foreach ($product['barcodes'] ?? [] as $bcode) {
                $key = current(array_keys($bcode));
                $value = current(array_values($bcode));
                $bcodes[$key] = $value;
            }

            Product::query()->updateOrInsert(
                [
                    'ext_id' => $product['id'],
                ],
                [
                    'name' => $product['name'],
                    'group_id' => $folderId,
                    'code' => $product['code'],
                    'ext_code' => $product['externalCode'],
                    'article' => $product['article'] ?? '',
                    'status' => $status,
                    'buy_price' => isset($product['buyPrice'])
                        ? $product['buyPrice']['value'] / 100
                        : 0,
                    'ean13' => $bcodes['ean13'] ?? '',
                    'gtin' => $bcodes['gtin'] ?? '',
                    'group_name' => $product['pathName'] ?? '',
                    'supplier_id' => $supplierId,
                    'has_images' => isset($product['images'])
                        && $product['images']['meta']['size'] > 0,
                    'brand' => $brand,
                    'stock' => $product['stock'] ?? 0,
                    'reserve' => $product['reserve'] ?? 0,
                    'quantity' => $product['quantity'] ?? 0,
                    'updated_at' => Carbon::parse($product['updated']),
                ],
            );

            array_map(
                static function (mixed $item) use ($product) {
                    Prices::query()->updateOrInsert(
                        [
                            'product_id' => $product['id'],
                            'type_id' => $item['priceType']['id'],
                        ],
                        [
                            'type_name' => $item['priceType']['name'],
                            'value' => $item['value'] / 100,
                        ],
                    );
                },
                $product['salePrices'],
            );
        }
    }
}
