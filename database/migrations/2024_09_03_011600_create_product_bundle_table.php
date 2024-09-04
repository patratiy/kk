<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductBundleTable extends Migration
{
    public function up()
    {
        Schema::create(
            'products_bundles',
            static function (Blueprint $table) {
                $table->string('ext_id')->primary();
                $table->string('product_id');
                $table->string('bundle_id');
                $table->integer('quantity');
            },
        );
    }

    public function down()
    {
        Schema::dropIfExists('products_bundles');
    }
}
