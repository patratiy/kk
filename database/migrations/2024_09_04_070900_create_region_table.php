<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionTable extends Migration
{
    public function up()
    {
        Schema::create(
            'region',
            static function (Blueprint $table) {
                $table->string('ext_id')->primary();
                $table->string('name');
                $table->string('code');
                $table->string('ext_code');

                $table->timestamp('updated_at');
            },
        );
    }

    public function down()
    {
        Schema::dropIfExists('region');
    }
}
