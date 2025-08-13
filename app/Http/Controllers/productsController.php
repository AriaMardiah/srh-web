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
    // Gunakan has('stocks') untuk memfilter di level database
    $products = Products::has('stocks') // <-- HANYA AMBIL PRODUK YANG PUNYA RELASI 'stocks'
        ->with('stocks') // <-- Tetap eager load untuk digunakan di accessor
        ->latest() // <-- Sebaiknya tambahkan pengurutan
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'images' => url('storage/' . $product->images),
                'price' => (int) $product->price,
                'variations'  => $product->grouped_stok,
            ];
        });

    // Gunakan ->values() untuk memastikan hasilnya adalah array JSON, bukan objek
    return response()->json($products->values());
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
            'image' => $product->images,
            'description' => $product->description,
            'price' => (float) $product->price,
        ]);
    }
}
