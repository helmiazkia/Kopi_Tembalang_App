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

            // Relasi ke Meja
            $table->foreignId('table_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('order_type', ['dine_in', 'takeaway'])
                ->default('takeaway');

            // Relasi ke User (Kasir/Admin yang melayani)
            $table->foreignId('cashier_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('customer_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();

            $table->integer('total_price');

            // Status yang disederhanakan
            $table->enum('status', [
                'pending',
                'paid',
                'preparing',
                'done',
                'cancelled'
            ])->default('pending');

            $table->boolean('is_printed')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
