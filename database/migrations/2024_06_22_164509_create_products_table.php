<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', static function (Blueprint $table) {
            $table->id();

            $table->string('ext_id');
            $table->string('name');
            $table->string('code');
            $table->string('ext_code');
            $table->string('article');
            $table->float('buy_price');
            $table->string('ean13');
            $table->string('gtin');

            //@todo images | one to many

            $table->boolean('has_images')->default(false);

            $table->string('supplier_id');
            $table->string('group_id');
            $table->string('group_name');

            $table->string('status');

            $table->string('brand');

            $table->integer('stock');
            $table->integer('reserve');
            $table->integer('quantity');

            $table->timestamp('updated_at');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
