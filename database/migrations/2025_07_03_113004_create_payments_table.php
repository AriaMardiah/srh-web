<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('order_id');
            $table->string('midtrans_transaction_id', 100)->nullable();
            $table->string('midtrans_order_id', 100)->unique()->nullable();
            $table->string('snap_token', 255)->nullable();
            $table->decimal('total_pembayaran', 8, 2);
            $table->string('metode_pembayaran', 50)->nullable();
            $table->enum('status_pembayaran', ['menunggu', 'diproses', 'selesai', 'gagal', 'kadaluarsa'])->default('menunggu');
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
