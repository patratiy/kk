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
        Schema::create(
            'order_status',
            static function (Blueprint $table) {
                $table->id();

                $table->string('ext_id');

                $table->string('name');
                $table->integer('color');
                $table->string('state_type');
            },
        );
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_status');
    }
};
