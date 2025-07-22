<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Cart::all()]);
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
        $request->validate([
            'id_transaksi' => 'nullable|exists:transaksi,id',
            'id_pembelian' => 'required|exists:pembelian,id',
        ]);

        $cart = Cart::create($request->only(['id_transaksi', 'id_pembelian']));

        if ($request->filled('id_transaksi')) {
            $pembelian = \App\Models\Pembelian::find($request->id_pembelian);
            if ($pembelian) {
                $pembelian->status = 0;
                $pembelian->save();
            }
        }

        return response()->json(['message' => 'Item added to cart', 'data' => $cart], 201);
    }


    public function destroy($id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        $cart->delete();
        return response()->json(['message' => 'Item removed from cart']);
    }
}
