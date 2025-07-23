<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Orders;
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
}
