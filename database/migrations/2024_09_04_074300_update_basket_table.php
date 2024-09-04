<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBasketTable extends Migration
{
    public function up()
    {
        Schema::table(
            'basket',
            static function (Blueprint $table) {
                $table->string('product_type')->nullable();
            },
        );
    }

    public function down()
    {
        if (
            Schema::hasTable('basket')
            && Schema::hasColumns(
                'basket',
                ['product_type',],
            )
        ) {
            Schema::dropColumns(
                'basket',
                ['product_type',],
            );
        }
    }
}
