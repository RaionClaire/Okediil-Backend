<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::all();
        return response()->json(['data' => $carts]);
    }

    public function show($id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        return response()->json(['data' => $cart]);
    }

    public function store(Request $request)
    {
        $cart = Cart::create($request->all());
        return response()->json(['message' => 'Item added to cart successfully', 'data' => $cart], 201);
    }

    public function destroy($id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        $cart->delete();
        return response()->json(['message' => 'Item removed from cart successfully']);
    }
}
