<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // Logic to retrieve cart items
        return response()->json(['message' => 'Cart items retrieved successfully']);
    }

    public function store(Request $request)
    {
        // Logic to add item to cart
        return response()->json(['message' => 'Item added to cart successfully'], 201);
    }

    public function destroy($id)
    {
        // Logic to remove item from cart
        return response()->json(['message' => 'Item removed from cart successfully']);
    }
}
