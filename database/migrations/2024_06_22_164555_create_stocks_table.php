<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateStocksTable extends Migration
{
    public function up()
    {
        Schema::create(
            'stocks',
            static function (Blueprint $table) {
                $table->string('product_id');
                $table->string('store_id');
                $table->string('store_name');
                $table->float('stock');
                $table->float('reserve');
                $table->float('in_transit');

                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
}
