<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create(
            'basket',
            static function (Blueprint $table) {
                $table->id();

                $table->string('ext_id');
                $table->string('order_id');
                $table->string('product_id');
                $table->integer('count');

                $table->integer('shipped');

                $table->timestamp('updated_at');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            },
        );
    }

    public function down()
    {
        Schema::dropIfExists('basket');
    }
}
