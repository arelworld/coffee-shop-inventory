<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('supplier_id')->nullable()->constrained();
            $table->foreignId('unit_id')->nullable()->constrained();
            $table->decimal('cost_per_unit', 10, 2)->default(0);
            $table->date('last_restock_date')->nullable();
            $table->string('sku')->nullable();
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['supplier_id', 'unit_id', 'cost_per_unit', 'last_restock_date', 'sku']);
        });
    }
};