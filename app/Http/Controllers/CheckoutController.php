<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\Products;
use App\Models\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        // Gunakan satu DB Transaction untuk membungkus semua logika
        return DB::transaction(function () use ($request) {

            $user = Auth::user();
            $items = collect($request['items']);

            // Deklarasikan variabel di dalam scope transaction
            $totalPrice = 0;
            $item_details_midtrans = [];

            // 2. Validasi stok dan hitung total harga
            // foreach ($items as $item) {
            //     $product = Products::find($item['product_id']);

            //     $variation = Stocks::find($item['variation_id']);
            //     if (!$variation) {
            //         throw new \Exception("Variasi untuk produk {$product->name} tidak valid.");
            //     }

            //     // Lakukan validasi stok
            //     if ($variation->stock > $item['quantity']) {
            //         throw new \Exception("Stok untuk produk {$product->name} (Ukuran: {$variation->size}, Warna: {$variation->color}) tidak mencukupi. Sisa {$variation->stock}.");
            //     }

            //     // Akumulasi total harga dan detail item untuk Midtrans
            //     $price = (int) $product->price;
            //     $quantity = (int) $item['quantity'];
            //     $totalPrice += $price * $quantity;

            //     $item_details_midtrans[] = [
            //         'id' => $item['variation_id'],
            //         'price' => $price,
            //         'quantity' => $quantity,
            //         'name' => "{$product->name} ({$variation->size}/{$variation->color})",
            //     ];
            // }

            // // Validasi akhir untuk total harga
            // if ($totalPrice <= 0) {
            //     throw new \Exception('Total harga transaksi tidak boleh nol.');
            // }

            // 3. Tentukan status order
            $status = ($request['payment_method'] === 'cod') ? 'Dikemas' : 'Belum Bayar';


            // 4. Buat Order baru
            $order = Orders::create([
                'user_id' => $user->id,
                'total' => $request->total_price,
                'status' => $status,
            ]);



            // 5. Buat Order Details dan catat pergerakan stok
            foreach ($items as $item) {
                // dd($item['variation_id']);
                $order->order_details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'order_id' => $order->id,
                    'color' => $item['color'],
                    'size' => $item['size'],

                ]);

                // Kurangi stok dari tabel variasi
                $variation = Stocks::find($item['variation_id']);
                // $variation->decrement('quantity', $item['quantity']);

                Stocks::create([
                    'product_id' => $item['product_id'],
                    'color' => $variation->color,
                    'size' => $variation->size,
                    'quantity' => $item['quantity'],
                    'status' => 'Keluar',
                ]);
            }

            // 6. Logika untuk Metode Pembayaran Digital (Midtrans)
            $paymentInfo = null;
            if ($request['payment_method'] === 'digital_payment') {
                Config::$serverKey = config('midtrans.server_key');
                Config::$isProduction = config('midtrans.is_production');
                Config::$isSanitized = true;
                Config::$is3ds = true;

                $params = [
                    'transaction_details' => [
                        'order_id' => $order->id . '-' . time(),
                        'gross_amount' =>  $order->total,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone_number,
                    ],
                    // 'item_details' => $item_details_midtrans,
                    "callbacks" => [
                        "finish" => "myapp://payment-finish"
                    ],

                ];

                $snapToken = Snap::getSnapToken($params);
            }
            $transaction_id = 'TRX-' . Str::random(4) . '-' . Str::random(8);
            $isCod = $request['payment_method'] === 'cod';
            $paymentStatus = $isCod ? 'menunggu' : 'diproses';
            $order->payments()->create([
                'midtrans_order_id' => $isCod ? null : $params['transaction_details']['order_id'],
                'order_id' => $transaction_id,
                'status_pembayaran' => $paymentStatus,
                'snap_token' => $isCod ? null : $snapToken,
                'total_pembayaran' => $order->total,
                'metode_pembayaran' => $request['payment_method'],
            ]);


            // 7. Kembalikan respons
            return response()->json([
                'message' => 'Checkout berhasil!',
                'order' => $order,
                'snap_token' => $isCod ? null : $snapToken,
                'order_details' => $order->order_details,
                // 'variations' => $product->grouped_stok,
            ], 201);
        });
    }




}
