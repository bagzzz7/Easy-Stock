<?php
// database/migrations/xxxx_add_category_type_and_unit_to_medicines_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medicines', function (Blueprint $table) {
            // Add category type (adults/children/both)
            $table->enum('category_type', ['adults', 'children', 'both'])
                  ->default('both')
                  ->after('category_id');
            
            // Add unit of measurement
            $table->enum('unit', ['tablet', 'capsule', 'ml', 'mg', 'g', 'mcg', 'unit'])
                  ->default('tablet')
                  ->after('quantity');
                  
            // Optional: Add strength (e.g., 500mg, 250mg/5ml)
            $table->string('strength')->nullable()->after('unit');
        });
    }

    public function down()
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn(['category_type', 'unit', 'strength']);
        });
    }
};