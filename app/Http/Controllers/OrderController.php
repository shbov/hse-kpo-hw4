<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'orders' => Order::with('dishes')->where('user_id', auth()->id())->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request): JsonResponse
    {
        $order = Order::create($request->all());

        $dishes = collect($request->dishes)->map(function ($item) {
            $dish = Dish::where('id', $item['id'])->first();
            $dish->quantity -= $item['quantity'];
            $dish->save();

            return [
                'id' => $item['id'],
                'meta' => [
                    'price' => $dish->price, // price per item
                    'quantity' => $item['quantity']
                ]
            ];
        });

        foreach ($dishes as $dish) {
            $order->dishes()->attach($dish['id'], $dish['meta']);
        }

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): JsonResponse
    {
        if (!$this->checkAccess($order)) {
            return response()->json([
                'message' => 'You don\'t have access to this order',
            ], 403);
        }

        return response()->json([
            'order' => $order,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order): JsonResponse
    {
        if (!$this->checkAccess($order)) {
            return response()->json([
                'message' => 'You don\'t have access to this order',
            ], 403);
        }

        $order->delete();
        return response()->json([
            'message' => 'Order deleted successfully',
            'order' => $order,
        ]);
    }

    /**
     * @param Order $order
     * @return bool
     */
    private function checkAccess(Order $order): bool
    {
        return $order->user_id === auth()->id();
    }
}
