<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('stocks.warehouse')->get();
        return response()->json($products);
    }

    public function getImage(string $id): Response
    {
        return new Response('', 200);
    }

    private static function getMethodPath(string $type, array $params = []): string
    {
        return match ($type) {
            'catalog' => '/entity/assortment',
            'orders' => '/entity/customerorder',
            'basket' => "/entity/customerorder/{$params['order_id']}/positions",
            'stores' => '/entity/store',
            'status' => '/entity/customerorder/metadata',
            'counterparty' => '/entity/counterparty',
            'counterparty_status' => '/entity/counterparty/metadata',
            'stocks' => '/report/stock/bystore',
            default => throw new \RuntimeException('Parameter type is mandatory, input type is not support!'),
        };
    }

    public function test(): Response
    {
        $path = static::getMethodPath('orders');

        $baseUrl = config('app.base_url_api_moy_sklad');

        $privateKey = config('app.moy_sklad');

        $http = Http::withHeaders(
            [
                'Authorization' => sprintf('Bearer %s', $privateKey),
                'Accept-Encoding' => 'gzip',
            ],
        );

        $response = $http->get(
            sprintf('%s%s', $baseUrl, $path),
            [
                'limit' => 10,
                'offset' => 0,
                'filter' => 'updated>2024-08-27 00:00:00.000'
//                'order' => 'updated,asc',
            ],
        );

        return new Response('', 200);
    }
}
