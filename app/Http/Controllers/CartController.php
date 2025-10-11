<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{


    public function store(Request $request)
    {
        $request->validate([
            'id_transaksi' => 'nullable|exists:transaksi,id_transaksi',
            'id_pembelian' => 'required|exists:pembelian,id_pembelian',
        ]);

        $cart = Cart::create($request->only(['id_transaksi', 'id_pembelian']));

        $pembelian = \App\Models\Pembelian::find($request->id_pembelian);
        if ($pembelian) {
            $pembelian->status = 0;
            $pembelian->save();
        }

        return response()->json(['message' => 'Item added to cart', 'data' => $cart], 201);
    }


    public function index()
    {
        return response()->json(['data' => Cart::orderBy('created_at', 'desc')->get()]);
    }

    public function show($id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        return response()->json(['data' => $cart]);
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

    public function update(Request $request, $id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $request->validate([
            'id_transaksi' => 'nullable|exists:transaksi,id_transaksi',
            'id_pembelian' => 'required|exists:pembelian,id_pembelian',
        ]);


        $cart->update($request->only(['id_transaksi', 'id_pembelian']));

        if ($request->filled('id_transaksi')) {
            $pembelian = \App\Models\Pembelian::find($request->id_pembelian);
            if ($pembelian) {
                $pembelian->status = 0;
                $pembelian->save();
            }
        }

        return response()->json(['message' => 'Cart updated successfully', 'data' => $cart]);
    }

    public function filter(Request $request)
    {
        $query = Cart::query();

        if ($request->has('id_transaksi')) {
            $query->where('id_transaksi', $request->id_transaksi);
        }

        if ($request->has('id_pembelian')) {
            $query->where('id_pembelian', $request->id_pembelian);
        }

        return response()->json($query->get(), 200);
    }
}