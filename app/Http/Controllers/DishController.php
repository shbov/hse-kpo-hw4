<?php

namespace App\Http\Controllers;

use App\Http\Requests\DishRequest;
use App\Http\Requests\DishUpdateRequest;
use App\Models\Dish;
use Illuminate\Http\JsonResponse;

class DishController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'dishes' => Dish::where('is_available', true)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DishRequest $request): JsonResponse
    {
        $dish = Dish::create($request->all());

        return response()->json([
            'message' => 'Dish created successfully',
            'dish' => $dish,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Dish $dish): JsonResponse
    {
        return response()->json([
            'dish' => $dish,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DishUpdateRequest $request, Dish $dish): JsonResponse
    {
        $dish->update($request->all());

        return response()->json([
            'message' => 'Dish updated successfully',
            'dish' => $dish,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dish $dish): JsonResponse
    {
        $dish->delete();

        return response()->json([
            'message' => 'Dish deleted successfully',
            'dish' => $dish,
        ]);
    }
}
