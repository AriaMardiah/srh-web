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
        Schema::create('model_request', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('title',100);
            $table->text('description');
            $table->string('file');
            $table->enum('status',['diterima','ditolak','diproses']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_request');
    }
};
