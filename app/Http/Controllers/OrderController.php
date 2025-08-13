<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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


        // 3. KEMBALIKAN DATA YANG SUDAH DIFORMAT
        return response()->json($orders);
    }
    public function cancel(Orders $order)
    {
        if (Auth::id() !== $order->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!in_array($order->status, ['Belum Bayar'])) {
            return response()->json(['message' => 'Order ini tidak dapat dibatalkan.'], 422);
        }

        return DB::transaction(function () use ($order) {
            $order->status = 'Dibatalkan';
            $order->save();

            $order->payments()->update(['status_pembayaran' => 'gagal']);

            foreach ($order->order_details as $detail) {
                $product = Products::find($detail->product_id);

                if (!$product) {
                    continue;
                }

                $groupedStokItem = $product->grouped_stok->first();
                if (!$groupedStokItem) {
                    continue;
                }

                $sourceStockId = $groupedStokItem['id'];

                $sourceStock = Stocks::find($sourceStockId);

                if ($sourceStock) {
                    Stocks::create([
                        'product_id' => $detail->product_id,
                        'color'      => $sourceStock->color,
                        'size'       => $sourceStock->size,
                        'quantity'   => $detail->quantity,
                        'status'     => 'Masuk',
                    ]);
                }
            }



            return response()->json([
                'message' => 'Order berhasil dibatalkan dan stok telah dikembalikan.',
                'order' => $order,
            ]);
        });
    }
}
