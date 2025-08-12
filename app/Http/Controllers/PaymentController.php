<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Config;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function handle(Request $request)
    {
        $data = $request->all();

        $midtransOrderId = $data['order_id'];

        $payment = Payments::where('midtrans_order_id', $midtransOrderId)->first();


        // Periksa apakah order ada di sistem sebelum memproses notifikasi
        if ($payment) {
            // Panggil method untuk update status dengan menggunakan ID order dari Midtrans
            $result = $this->updateOrderStatusBasedOnMidtransResponse($midtransOrderId, $data);
            Log::info('Notifikasi Midtrans berhasil diproses', ['result' => $result]);
        } else {
            // Catat log jika order tidak ditemukan, ini bisa menjadi indikasi masalah
            Log::warning('Notifikasi Midtrans untuk order yang tidak ada diterima.', ['order_id' => $midtransOrderId]);
        }

        // Selalu kembalikan response 200 OK ke Midtrans agar tidak mengirim notifikasi berulang
        return response()->json([
            'status' => 'success',
            'message' => 'OK'
        ], 200);
    }

    public function updateOrderStatusBasedOnMidtransResponse($midtransOrderId, $data)
    {
        $payments = payments::where('midtrans_order_id', $midtransOrderId)->first();

        if (!$payments) {
            Log::warning("Order tidak ditemukan untuk midtrans_order_id: " . $midtransOrderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = $data['transaction_status'] ?? null;
        $fraudStatus = $data['fraud_status'] ?? null;
        $payment_type = $data['payment_type'] ?? null;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $payments->status_pembayaran = 'selesai';
                $payments->metode_pembayaran = $payment_type;
            }
        } elseif ($transactionStatus === 'settlement') {
            $payments->status_pembayaran = 'selesai';
            $payments->metode_pembayaran = $payment_type;
        } elseif ($transactionStatus === 'pending') {
            $payments->status_pembayaran = 'diproses';
        } elseif ($transactionStatus === 'expire') {
            $payments->status_pembayaran = 'kadaluarsa';
        } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
            $payments->status_pembayaran = 'gagal';
        }

        // 4. Simpan perubahan ke database
        $payments->save();
        $order = Orders::where('id', $payments->order_id )->first();

        if ($order) {
            if (in_array($payments->status_pembayaran, ['selesai'])) {
                $order->status = 'Dikemas';
            } elseif ($payments->status_pembayaran === 'diproses') {
                $order->status = 'Belum Bayar';
            } elseif (in_array($payments->status_pembayaran, ['kadaluarsa', 'gagal'])) {
                $order->status = 'Dibatalkan';
            }

            $order->save();
        } else {
            Log::warning("Order tidak ditemukan untuk payment_id: " . $payments->id);
        }

        // 5. Beri respons OK ke Midtrans
        return response()->json(['message' => 'Notification handled successfully'], 200);
    }
}
