<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressOrderTable extends Migration
{
    public function up()
    {
        Schema::create(
            'address_order',
            static function (Blueprint $table) {
                $table->string('order_id')->primary();
                $table->string('full_address');
                $table->string('city');
                $table->string('region_id');
            },
        );
    }

    public function down()
    {
        Schema::dropIfExists('address_order');
    }
}
