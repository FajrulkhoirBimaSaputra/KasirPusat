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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // kasir
            $table->integer('total');
            $table->integer('uang_bayar')->nullable();
            $table->integer('uang_kembali')->nullable();
            $table->enum('payment_method', ['cash', 'qris']);
            $table->string('payment_status')->default('paid');
            $table->string('snap_token')->nullable();
            $table->boolean('with_receipt')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
