<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Validasi input status
        $request->validate([
            'status' => 'sometimes|in:Belum Bayar,Dikemas,Dikirim,Selesai',
        ]);

        $user = Auth::user();
        // Mulai query untuk order milik user yang login
        $query = Orders::where('user_id', $user->id);

        // Filter berdasarkan status jika ada
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Ambil data dengan relasi untuk efisiensi (Eager Loading)
        // dan urutkan dari yang terbaru
        $orders = $query->with('order_details.product')
                         ->latest()
                         ->get();

        return response()->json($orders);
    }
    public function process(Request $request)
    {
        // 1. Validasi input dari Flutter
        $validated = $request->validate([
            'payment_method' => 'required|in:cod,digital_payment',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variation_id' => 'required|integer', // Asumsi ada ID unik untuk setiap variasi
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $items = collect($validated['items']);
        $totalPrice = 0;
dd(Auth::user());

            $totalPrice = 0;
            foreach ($items as $item) {
                $product = Products::find($item['product_id']);

                // TODO: Dapatkan variasi spesifik berdasarkan $item['variation_id']
                // Lakukan validasi stok di sini. Jika stok tidak cukup, throw exception.
                // $variation = ProductVariation::find($item['variation_id']);
                // if ($variation->stock < $item['quantity']) {
                //     throw new \Exception("Stok untuk produk {$product->name} tidak mencukupi.");
                // }

                $totalPrice += $product->price * $item['quantity'];
            }

            // 3. Tentukan status order berdasarkan metode pembayaran
            $status = ($validated['payment_method'] === 'cod') ? 'Dikemas' : 'Belum Bayar';

            // 4. Buat Order baru
            $order = Orders::create([
                'user_id' => $user->id,
                'total' => $totalPrice,
                'status' => $status,
            ]);

            // 5. Buat Order Details dan catat pergerakan stok
            foreach ($items as $item) {
                $order->orderDetails()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    // Anda mungkin perlu menyimpan detail variasi juga
                ]);

                // Kurangi stok dengan mencatat pergerakan 'keluar'
                // TODO: Pastikan ini merujuk ke ID variasi yang benar
                Stocks::create([
                    'product_variation_id' => $item['variation_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'keluar',
                ]);
            }

            // 6. Logika untuk Metode Pembayaran
            $paymentInfo = null;
            if ($validated['payment_method'] === 'digital_payment') {
                // --- INTEGRASI MIDTRANS ---
                // Set Midtrans server key
                // Config::$serverKey = config('services.midtrans.server_key');
                // ... (konfigurasi lainnya) ...

                // $params = [ ... data transaksi ... ];
                // $snapToken = Snap::getSnapToken($params);
                // $paymentInfo = ['snap_token' => $snapToken];
                // Untuk sekarang kita simulasi saja
                $paymentInfo = ['snap_url' => 'https://app.sandbox.midtrans.com/snap/v1/transactions/...'];
            }

            return response()->json([
                'message' => 'Checkout berhasil!',
                'order' => $order,
                'payment_info' => $paymentInfo,
            ], 201);
    }
}
