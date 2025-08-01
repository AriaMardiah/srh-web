<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// Jika menggunakan Midtrans, import library-nya
// use Midtrans\Config;
// use Midtrans\Snap;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cod,digital_payment',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variation_id' => 'required|integer', // Asumsi ada ID unik untuk setiap variasi
            'items.*.quantity' => 'required|integer|min:1',
        ]);
    }

    //  public function handle(Request $request)
    // {
    //     // 1. Konfigurasi Midtrans
    //     Config::$serverKey = config('midtrans.server_key');
    //     Config::$isProduction = config('midtrans.is_production');

    //     try {
    //         // 2. Buat instance notifikasi dari input JSON
    //         $notification = new Notification();

    //         // 3. Ambil ID order dari notifikasi
    //         // Kita perlu memisahkan timestamp yang kita tambahkan sebelumnya
    //         $orderId = explode('-', $notification->order_id)[0];
    //         $order = Orders::findOrFail($orderId);

    //         // 4. Lakukan verifikasi signature key (keamanan)
    //         // Ini untuk memastikan notifikasi benar-benar dari Midtrans
    //         $signature = hash('sha512', $notification->order_id . $notification->status_code . $notification->gross_amount . config('midtrans.server_key'));
    //         if ($signature !== $notification->signature_key) {
    //             return response()->json(['message' => 'Invalid signature'], 403);
    //         }

    //         // 5. Handle status transaksi
    //         $transactionStatus = $notification->transaction_status;
    //         $fraudStatus = $notification->fraud_status;

    //         if ($transactionStatus == 'capture') {
    //             if ($fraudStatus == 'accept') {
    //                 // Transaksi berhasil dan aman
    //                 $order->status = 'Dikemas';
    //             }
    //         } else if ($transactionStatus == 'settlement') {
    //             // Transaksi berhasil diselesaikan
    //             $order->status = 'Dikemas';
    //         } else if ($transactionStatus == 'pending') {
    //             // Transaksi masih menunggu pembayaran
    //             $order->status = 'Belum Bayar';
    //         } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
    //             // Transaksi gagal atau dibatalkan
    //             $order->status = 'Dibatalkan';
    //         }

    //         // 6. Simpan perubahan status order
    //         $order->save();

    //         // 7. Beri respons OK ke Midtrans
    //         return response()->json(['message' => 'Notification handled successfully'], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }
}
