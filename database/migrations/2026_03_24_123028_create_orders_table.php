<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // 🔥 FIX DI SINI
            $table->foreignId('table_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('order_type', ['dine_in', 'takeaway'])
                ->default('dine_in');

            $table->foreignId('cashier_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('customer_name');
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();

            $table->integer('total_price');

            $table->enum('status', [
                'pending',
                'paid',
                'preparing',
                'ready',
                'done',
                'cancelled'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
