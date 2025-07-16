<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    // -------------------------------
    // Ambil semua produk
    // -------------------------------
    public function index()
    {
        $products = Products::all()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'images' => url('storage/' . $product->images),
                'description' => $product->description,
                'price' => (float) $product->price, // Ubah jadi float
            ];
        });
        return response()->json($products);
    }


    // -------------------------------
    // Simpan produk baru
    // -------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:30',
            'images'      => 'required|string',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
        ]);

        $product = Products::create([
            'name'        => $request->name,
            'images'      => $request->images,
            'description' => $request->description,
            'price'       => $request->price,
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'product' => $product,
        ], 201);
    }

    // -------------------------------
    // Update data produk
    // -------------------------------
    public function update(Request $request, Products $product)
    {
        $request->validate([
            'name'        => 'required|string|max:30',
            'images'      => 'required|string',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
        ]);

        $product->update([
            'name'        => $request->name,
            'images'      => $request->images,
            'description' => $request->description,
            'price'       => $request->price,
        ]);

        return response()->json([
            'message' => 'Produk berhasil diupdate',
            'product' => $product,
        ]);
    }

    // -------------------------------
    // Hapus produk
    // -------------------------------
    public function destroy(Products $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus',
        ]);
    }

    // -------------------------------
    // Tampilkan detail satu produk
    // -------------------------------
    public function show(Products $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'images' => $product->images,
            'description' => $product->description,
            'price' => (float) $product->price,
        ]);
    }
}
