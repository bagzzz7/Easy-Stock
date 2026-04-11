<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['stock_in', 'stock_out']);
            $table->enum('reason', [
                'purchase',       // stock in  — bought from supplier
                'adjustment',     // stock in  — manual correction
                'return',         // stock in  — returned item
                'sale',           // stock out — sold to customer
                'expired',        // stock out — disposed expired stock
                'damaged',        // stock out — damaged/lost
                'transfer',       // stock out — transferred out
            ]);
            $table->integer('quantity');           // always positive
            $table->integer('quantity_before');    // snapshot for audit
            $table->integer('quantity_after');     // snapshot for audit
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();  // purchase price for stock_in
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};