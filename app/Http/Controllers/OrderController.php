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
    // public function index(Request $request)
    // {
    //     // Validasi input status
    //     $request->validate([
    //         'status' => 'sometimes|in:Belum Bayar,Dikemas,Dikirim,Selesai',
    //     ]);

    //     $user = Auth::user();
    //     // Mulai query untuk order milik user yang login
    //     $query = Orders::where('user_id', $user->id);

    //     // Filter berdasarkan status jika ada
    //     if ($request->has('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     // Ambil data dengan relasi untuk efisiensi (Eager Loading)
    //     // dan urutkan dari yang terbaru
    //     $orders = $query->with('order_details.products')
    //                      ->latest()
    //                      ->get();

    //     return response()->json($orders);

    // //     $orders = $query->with('order_details.products')->latest()->get();

    // // $orders = $orders->map(function ($order) {
    // //     $order->order_details = $order->order_details->map(function ($detail) {
    // //         if ($detail->products) {
    // //             $detail->products->images = $detail->products->images
    // //                 ? url('storage/' . $detail->products->images)
    // //                 : null;
    // //         }
    // //         return $detail;
    // //     });
    // //     return $order;
    // // });

    // return response()->json($orders);
    // }

    public function index(Request $request)
{
    // ... (validasi tidak berubah) ...

    $user = Auth::user();
    $query = Orders::where('user_id', $user->id);

    if ($request->has('status')) {
        $query->where('status', $request->status);
    }

    // 1. TAMBAHKAN 'payment' KE DALAM EAGER LOADING
    $orders = $query->with(['order_details.products', 'payments'])
                     ->latest()
                     ->get();

    // 2. GUNAKAN 'map' UNTUK MENYUSUN ULANG DATA
    $formattedOrders = $orders->map(function ($order) {
        return [
            'id' => $order->id,
            'status' => $order->status,
            'total' => $order->total,
            'created_at' => $order->created_at,
            // 'payment_method' => $order->payments->metode_pembayaran,
            'order_details' => $order->order_details,
        ];
    });

    // 3. KEMBALIKAN DATA YANG SUDAH DIFORMAT
    return response()->json($orders);
}

}
