<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create(
            'bundles',
            static function (Blueprint $table) {
                $table->id();

                $table->string('ext_id');
                $table->string('name');
                $table->string('code');
                $table->string('ext_code');
                $table->string('article');
                $table->string('ean13');
                $table->string('gtin');
                $table->string('brand');

                $table->string('group_name');
                $table->string('group_id');

                $table->timestamp('updated_at');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            },
        );
    }

    public function down()
    {
        Schema::dropIfExists('bundles');
    }
};
