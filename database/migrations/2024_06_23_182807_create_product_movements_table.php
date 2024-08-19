<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('product_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamp('movement_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('type'); // 'addition' or 'subtraction'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_movements');
    }
}

