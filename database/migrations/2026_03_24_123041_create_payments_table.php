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
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('transaction_id')->nullable();
            $table->text('snap_token')->nullable();

            // 🔥 METODE
            $table->enum('method', [
                'cash',
                'qris',
                'ewallet',
                'va',
                'card'
            ]);

            $table->string('channel')->nullable();
            $table->integer('amount');

            // 🔥 STATUS
            $table->enum('status', [
                'pending',
                'paid',
                'failed',
                'expired'
            ])->default('pending');

            // 🔥 KOLOM BARU: UNTUK TIMEOUT PEMBAYARAN DIGITAL
            $table->timestamp('expired_at')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
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
