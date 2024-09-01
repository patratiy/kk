<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('basket')) {
            Schema::table(
                'basket',
                static function (Blueprint $table) {
                    $table->float('sale_price', 15);
                    $table->float('discount', 15)->nullable();
                    $table->float('buy_price', 15)->nullable();
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (
            Schema::hasColumns(
                'basket',
                ['sale_price', 'discount', 'buy_price'],
            )
        ) {
            Schema::dropColumns(
                'basket',
                ['sale_price', 'discount', 'buy_price'],
            );
        }
    }
};
