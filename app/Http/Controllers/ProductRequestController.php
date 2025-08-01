<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ModelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductRequestController extends Controller
{
    // app/Http/Controllers/Api/ProductRequestController.php

public function index(Request $request)
{
    // Validasi input status
    $request->validate([
        'status' => 'required|in:diproses,diterima,ditolak',
    ]);

    $user = Auth::user();

   $requests = ModelRequest::where('user_id', $user->id)
        ->where('status', $request->status)
        ->with('product') // Tetap load relasi product
        ->latest()
        ->get();

    $formattedRequests = $requests->map(function ($productRequest) {
        return [
            'id' => $productRequest->id,
            'status' => $productRequest->status,
            'title' => $productRequest->title,
            'description' => $productRequest->description,
            'file_url' => $productRequest->file ? url('storage/' . $productRequest->file) : null,

            // INI KUNCINYA: Cek apakah relasi product ada sebelum mengaksesnya
            'product' => $productRequest->product ? [
                'id' => $productRequest->product->id,
                'name' => $productRequest->product->name,
                'image' => url('storage/products/' . $productRequest->product->images),
            ] : null, // Jika tidak ada produk, kirim null
        ];
    });

    return response()->json($formattedRequests);
}

    public function getProductsFromRequests(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil semua request milik user yang statusnya 'Diterima'
        //    dan pastikan produknya ada (eager loading).
        $requests = ModelRequest::where('user_id', $user->id)
            ->where('status', 'diterima') // Filter hanya yang diterima
            ->with('product.stocks') // Eager load produk & variasinya
            ->whereHas('product') // Pastikan produknya masih ada
            ->get();

        // 2. "Tarik keluar" hanya data produk dari setiap request.
        //    'pluck' akan membuat koleksi baru yang hanya berisi objek produk.
        $products = $requests->pluck('product');

        // // 3. (Opsional tapi direkomendasikan) Format data produk agar konsisten
        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'image' => url('storage/' . $product->images),
                'price' => (int) $product->price,
                'variations' => $product->grouped_stok,
            ];
        });

        // 4. Kembalikan daftar produk sebagai JSON
        return response()->json($formattedProducts);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // file opsional
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('public/requests');
            $filePath = basename($filePath);
        }

        $user = Auth::user();

        $newRequest = ModelRequest::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'file' => $filePath,
            'user_id' => $user->id,
            'status' => 'diproses', // Status default saat pertama kali dibuat
        ]);

        return response()->json([
            'message' => 'Request berhasil dibuat!',
            'data' => $newRequest,
        ], 201);
    }
}
