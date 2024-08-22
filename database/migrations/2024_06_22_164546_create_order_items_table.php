<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('basket', function (Blueprint $table) {
            $table->id();

            $table->string('ext_id');
            $table->string('order_id');
            $table->string('product_id');
            $table->integer('count');

            $table->integer('shipped');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('basket');
    }
}
