<?php

namespace App\Console\Commands;

use App\Models\Basket;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Prices;
use App\Models\Product;
use App\Models\Stores;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use SplFixedArray;

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

        $path = static::getMethodPath($this->argument('type'));

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

        //  curl -X GET
        //  "https://api.moysklad.ru/api/remap/1.2/entity/customerorder"
        //  -H "Authorization: Bearer <Credentials>"
        //  -H "Accept-Encoding: gzip"

        $start = Carbon::now()->getTimestamp();

        $response = $http->get(
            static::getUri($path),
            [
                'limit' => $this->limit,
                'offset' => $this->offset,
                'order' => 'updated,asc',
            ],
        );

//        Log::info(print_r($response->json(), true));

        while ($response['meta']['size'] > $this->offset) {
            match ($this->argument('type')) {
                'catalog' => static::processCatalogData($response->json()),
                'orders' => static::processOrderData($response->json(), $http),
                'stores' => static::processStoreData($response->json()),
                'status' => static::processDictData($response->json()),
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
            'Time spend on execution sync',
            [
                'type' => $this->argument('type'),
                'sec' => Carbon::now()->getTimestamp() - $start,
            ],
        );

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

    private static function getMethodPath(string $type, array $params = []): string
    {
        return match ($type) {
            'catalog' => '/entity/assortment',
            'orders' => '/entity/customerorder',
            'basket' => "/entity/customerorder/{$params['order_id']}/positions",
            'stores' => '/entity/store',
            'status' => '/entity/customerorder/metadata',
            default => throw new RuntimeException('Parameter type is mandatory, input type is not support!'),
        };
    }

    private static function getValueAttributeById(
        array  $params,
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

    private static function processStoreData(array $data): void
    {
        if (empty($data['rows'])) {
            return;
        }

        foreach ($data['rows'] as $store) {
            Stores::query()->updateOrInsert(
                [
                    'ext_id' => $store['id'],
                ],
                [
                    'name' => $store['name'],
                    'address' => $store['address'] ?? '',
                    'ext_code' => $store['externalCode'],
                    'updated_at' => Carbon::parse($store['updated']),
                ],
            );
        }
    }

    private static function processDictData(array $data): void
    {
        if (empty($data['states'])) {
            return;
        }

        foreach ($data['states'] as $states) {
            OrderStatus::query()->updateOrInsert(
                [
                    'ext_id' => $states['id'],
                ],
                [
                    'name' => $states['name'],
                    'color' => $states['color'],
                    'state_type' => $states['stateType'],
                ],
            );
        }
    }

    private static function processOrderData(array $data, PendingRequest $request): void
    {
        if (empty($data['rows'])) {
            return;
        }

        //@todo временный код, удалить после синхры
        $orders = Order::query()->where(
            'updated_at',
            '>',
            Carbon::parse('2024-01-01'),
        )->get(['ext_id'])->toArray();

        $orderIds = array_column($orders, 'ext_id');

        $array = new SplFixedArray();
        $array->setSize(count($orderIds));

        $speedImprove = array_combine($orderIds, $array->toArray());

        foreach ($data['rows'] as $order) {
            if (array_key_exists($order['id'], $speedImprove)) {
                continue;
            }

            $countPosition = $order['positions']['meta']['size'] ?? 0;

            $storeId = isset($order['store'])
                ? static::extractGuidFromUri($order['store']['meta']['href'])
                : '';

            $customerId = isset($order['agent'])
                ? static::extractGuidFromUri($order['agent']['meta']['href'])
                : '';

            $statusId = isset($order['state'])
                ? static::extractGuidFromUri($order['state']['meta']['href'])
                : '';

            $paymentType = static::getValueAttributeById(
                params: $order,
                attrId: '4c61c702-d33f-11eb-0a80-093100099c0d',
            );

            $deliveryType = static::getValueAttributeById(
                params: $order,
                attrId: '15fd32b9-d056-11eb-0a80-0dc400121910',
            );

            $customerType = static::getValueAttributeById(
                params: $order,
                attrId: 'c8a630e2-d057-11eb-0a80-01ef0011bcc2',
            );

            $hasClosedDocuments = static::getValueAttributeById(
                params: $order,
                attrId: 'Закрывающие документы получены',
            );

            Order::query()->updateOrInsert(
                [
                    'ext_id' => $order['id'],
                ],
                [
                    'order_number' => $order['name'],
                    'ext_code' => $order['externalCode'],
                    'code' => $order['code'] ?? '',
                    'goods_count' => $countPosition,

                    'order_price' => $order['sum'] / 100,
                    'paid_sum' => $order['payedSum'] / 100,
                    'shipped_sum' => $order['shippedSum'] / 100,
                    'invoiced_sum' => $order['invoicedSum'] / 100,

                    'store_id' => $storeId,
                    'customer_id' => $customerId,
                    'status_id' => $statusId,

                    'payment_type' => $paymentType,
                    'delivery_type' => $deliveryType,
                    'customer_type' => $customerType,
                    'has_closed_documents' => $hasClosedDocuments ? 1 : 0,

                    'updated_at' => Carbon::parse($order['updated']),
                    'created_at' => Carbon::parse($order['created']),
                ],
            );

            if (empty($countPosition)) {
                continue;
            }

            //делаем паузу, что бы не спамить сервис, 300 мл.сек
            sleep(3);

            $path = static::getMethodPath('basket', ['order_id' => $order['id']]);

            $response = $request->get(
                static::getUri($path),
                [
                    'limit' => 100,
                    'offset' => 0,
                ],
            );

            if ($response->status() === 429) {
                Log::error('Service response with 429', ['order_id' => $order['id']]);
                sleep(10);
            }

            $basket = $response->json();

            array_map(
                static function (mixed $item) use ($order) {
                    $productId = isset($item['assortment'])
                        ? static::extractGuidFromUri($item['assortment']['meta']['href'])
                        : '';

                    Basket::query()->updateOrInsert(
                        [
                            'order_id' => $order['id'],
                            'product_id' => $productId,
                        ],
                        [
                            'ext_id' => $item['id'],
                            'count' => (int)$item['quantity'],
                            'shipped' => (int)$item['shipped'],
                            'updated_at' => Carbon::parse($order['updated']),
                        ],
                    );
                },
                $basket['rows'] ?? [],
            );
        }
    }

    private static function processCatalogData(array $data): void
    {
        if (empty($data['rows'])) {
            return;
        }
        //@todo ускорить синхронизацию, путем анализа даты обновления, брать с запасом, - 2 суток от даты старта скрипта
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

            //@todo реализовать работу с бандлами

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
