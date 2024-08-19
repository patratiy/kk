<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductMovement;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\OrderStatus;
use App\Models\TypeMove;

class OrderController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('customer')) {
            $query->where('customer', 'like', '%' . $request->customer . '%');
        }

        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        $orders = $query->paginate($request->get('per_page', 15));

        return response()->json($orders->items());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (!$request->customer || empty($request->items) || !$request->warehouse_id) {
            return response()->json(['error' => 'Не переданы обязательные параметры'], 400);
        }

        $order = Order::create([
            'customer' => $request->customer,
            'warehouse_id' => $request->warehouse_id,
            'status' => OrderStatus::Active->value,
        ]);

        foreach ($request->items as $item) {
            $orderItem = new OrderItem([
                'product_id' => $item['product_id'],
                'count' => $item['count'],
            ]);

            $order->items()->save($orderItem);

            // Запись движения
            ProductMovement::create([
                'product_id' => $item['product_id'],
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $item['count'],
                'type' => TypeMove::Sub->value,
            ]);

            // Обновление остатков
            $stock = Stock::where('product_id', $item['product_id'])
                ->where('warehouse_id', $request->warehouse_id)
                ->first();

            if (!$stock || $stock->stock < $item['count']) {
                return response()->json(['error' => 'Неверное количество товар на складе для товара ' . $item['product_id']], 400);
            }

            $stock->stock -= $item['count'];
            $stock->save();
        }

        return response()->json($order);
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Order $order)
    {
        if (!$request->customer || empty($request->items) || !$request->warehouse_id) {
            return response()->json(['error' => 'Не переданы обязательные параметры'], 400);
        }

        $order->customer = $request->customer;
        $order->save();

        $existingItems = $order->items;
        foreach ($existingItems as $existingItem) {
            $existingItem->delete();

            // Восстановление остатков
            ProductMovement::create([
                'product_id' => $existingItem->product_id,
                'warehouse_id' => $order->warehouse_id,
                'quantity' => $existingItem->count,
                'type' => 'addition',
            ]);

            $stock = Stock::where('product_id', $existingItem->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->first();
            $stock->stock += $existingItem->count;
            $stock->save();
        }

        foreach ($request->items as $item) {
            $orderItem = new OrderItem([
                'product_id' => $item['product_id'],
                'count' => $item['count'],
            ]);

            $order->items()->save($orderItem);

            // Запись движения
            ProductMovement::create([
                'product_id' => $item['product_id'],
                'warehouse_id' => $order->warehouse_id,
                'quantity' => $item['count'],
                'type' => TypeMove::Sub->value,
            ]);

            // Обновление остатков
            $stock = Stock::where('product_id', $item['product_id'])
                ->where('warehouse_id', $order->warehouse_id)
                ->first();

            if (!$stock || $stock->stock < $item['count']) {
                return response()->json(['error' => 'Неверное кол. товара для ' . $item['product_id']], 400);
            }

            $stock->stock -= $item['count'];
            $stock->save();
        }

        return response()->json($order);
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Order $order)
    {
        if ($order->status !== OrderStatus::Active->value) {
            return response()->json(['error' => 'Заказ не в активном статуе'], 500);
        }

        $order->status = OrderStatus::Completed->value;
        $order->completed_at = now();

        if (!$order->save()) {
            return response()->json(['error' => 'Не удалось изменить статус заказа'], 500);
        }

        return response()->json($order);
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Order $order)
    {
        if ($order->status === OrderStatus::Canceled->value) {
            return response()->json(['error' => 'Заказ уже отменен'], 500);
        }

        $order->status = OrderStatus::Canceled->value;
        $order->save();

        foreach ($order->items as $item) {
            ProductMovement::create([
                'product_id' => $item->product_id,
                'warehouse_id' => $order->warehouse_id,
                'quantity' => $item->count,
                'type' => TypeMove::Add->value,
            ]);

            $stock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->first();

            $stock->stock += $item->count;
            $stock->save();
        }

        return response()->json($order);
    }

    /**
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function resume(Order $order)
    {
        if ($order->status !== OrderStatus::Canceled->value) {
            return response()->json(['error' => 'Заказ находится не в статусе "отменен"'], 500);
        }

        foreach ($order->items as $item) {
            $stock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->first();

            if (!$stock || $stock->stock < $item->count) {
                return response()->json(['error' => 'Неверное количество товара для ' . $item->product_id], 400);
            }
        }

        $order->status = OrderStatus::Active->value;
        $order->save();

        foreach ($order->items as $item) {
            ProductMovement::create([
                'product_id' => $item->product_id,
                'warehouse_id' => $order->warehouse_id,
                'quantity' => $item->count,
                'type' => TypeMove::Sub->value,
            ]);

            $stock = Stock::where('product_id', $item->product_id)
                ->where('warehouse_id', $order->warehouse_id)
                ->first();

            $stock->stock -= $item->count;
            $stock->save();
        }

        return response()->json($order);
    }
}


