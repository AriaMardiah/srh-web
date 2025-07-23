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
    // Gunakan eager loading 'with()' untuk mengambil produk beserta variasinya secara efisien
    $products = Products::with('stocks')->get()->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            // 'image' bukan 'images'. Sesuaikan dengan nama kolom di database Anda.
            'image' => url('storage/' . $product->images),
            'price' => (int) $product->price,
            'variations'  => $product->grouped_stok,
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
