<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number')->unique();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->enum('source', ['online', 'walk-in']);
            $table->string('customer_name')->nullable()->default('Umum');
            $table->decimal('total', 14, 2)->default(0);
            $table->enum('payment_method', ['cash', 'transfer']);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('change', 14, 2)->default(0);
            $table->enum('status', ['completed', 'voided'])->default('completed');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
