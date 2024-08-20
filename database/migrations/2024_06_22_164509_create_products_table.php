<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('ext_id');
            $table->string('name');
            $table->string('code');
            $table->string('ext_code');
            $table->string('article');
            $table->float('buy_price');
            $table->string('ean13');

            //sale | price one to many

            //images | one to many

            $table->string('supplier');

            $table->string('brand');

            $table->integer('stock');
            $table->integer('reserve');
            $table->integer('quantity');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
