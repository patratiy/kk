<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            'counterparty',
            static function (Blueprint $table) {
                $table->id();

                $table->string('ext_id');

                $table->string('name');
                $table->string('description', 3000);

                $table->string('status_id');

                $table->string('phone');
                $table->string('email');

                $table->string('legal_middle_name');
                $table->string('legal_first_name');
                $table->string('legal_last_name');
                $table->string('ogrnip');
                $table->string('ogrn');
                $table->string('okpo');
                $table->string('inn');
                $table->string('actual_address');
                $table->string('legal_address');
                $table->string('company_name');
                $table->string('company_type');

                $table->string('ext_code');

                $table->float('sales_amount', 15);
                $table->string('tags');

                $table->timestamp('updated_at');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('counterparty');
    }
};
