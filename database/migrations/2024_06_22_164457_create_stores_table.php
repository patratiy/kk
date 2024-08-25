<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateStoresTable extends Migration
{
    public function up()
    {
        Schema::create(
            'stores',
            static function (Blueprint $table) {
                $table->id();

                $table->string('ext_id');

                $table->string('name');
                $table->string('address');
                $table->string('ext_code');

                $table->timestamp('updated_at');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            },
        );
    }

    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
