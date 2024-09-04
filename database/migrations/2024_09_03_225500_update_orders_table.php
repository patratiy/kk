<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrdersTable extends Migration
{
    public function up()
    {
        Schema::table(
            'orders',
            static function (Blueprint $table) {
                $table->string('site_id')->nullable();
                $table->string('source_id')->nullable();
                $table->string('order_source')->nullable();
                $table->string('reason_cancel', 500)->default('');
            },
        );
    }

    public function down()
    {
        if (
            Schema::hasTable('orders')
            && Schema::hasColumns(
                'orders',
                ['site_id', 'source_id', 'order_source', 'reason_cancel'],
            )
        ) {
            Schema::dropColumns(
                'orders',
                ['site_id', 'source_id', 'order_source', 'reason_cancel'],
            );
        }
    }
}
