<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_perishable')->default(false);
            $table->date('expiry_date')->nullable();
            $table->integer('shelf_life_days')->nullable()->comment('Shelf life in days after opening/manufacture');
            $table->date('manufacture_date')->nullable();
            $table->string('batch_number')->nullable();
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'is_perishable', 
                'expiry_date', 
                'shelf_life_days', 
                'manufacture_date', 
                'batch_number'
            ]);
        });
    }
};