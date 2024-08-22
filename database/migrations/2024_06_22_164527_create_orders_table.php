<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('ext_id');

            $table->string('order_number');
            $table->string('ext_code');
            $table->string('code');
            $table->integer('goods_count');

            $table->float('order_price', 15);
            $table->float('paid_sum', 15)->default(.0);
            $table->float('shipped_sum', 15)->default(.0);
            $table->float('invoiced_sum', 15)->default(.0);

            $table->string('store_id');
            $table->string('customer_id');

            $table->string('status_id');

            $table->string('payment_type');
            $table->string('delivery_type');
            $table->string('customer_type');
            $table->boolean('has_closed_documents')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
