<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndexesTable extends Migration
{
    public function up()
    {
        Schema::table(
            'address_order',
            static function (Blueprint $table) {
                $table->index('order_id', 'address_order_order_id_idx');
                $table->index('region_id', 'address_order_region_id_idx');
            },
        );

        Schema::table(
            'basket',
            static function (Blueprint $table) {
                $table->index('ext_id', 'basket_external_id_idx');
                $table->index('order_id', 'basket_order_id_idx');
                $table->index('product_id', 'basket_product_id_idx');
            },
        );

        Schema::table(
            'bundles',
            static function (Blueprint $table) {
                $table->index('ext_id', 'bundles_ext_id_idx');
            },
        );

        Schema::table(
            'counterparty',
            static function (Blueprint $table) {
                $table->index('ext_id', 'counterparty_ext_id_idx');
                $table->index('status_id', 'counterparty_status_id_idx');
            },
        );

        Schema::table(
            'counterparty_status',
            static function (Blueprint $table) {
                $table->index('ext_id', 'counterparty_status_ext_id_idx');
            },
        );

        Schema::table(
            'order_status',
            static function (Blueprint $table) {
                $table->index('ext_id', 'order_status_ext_id_idx');
                $table->index('name', 'order_status_name_idx');
            },
        );

        Schema::table(
            'orders',
            static function (Blueprint $table) {
                $table->index('ext_id', 'orders_ext_id_idx');
                $table->index('customer_id', 'orders_customer_id_idx');
                $table->index('ext_code', 'orders_ext_code_idx');
                $table->index('status_id', 'orders_status_id_idx');
                $table->index('store_id', 'orders_store_id_idx');
                $table->index('updated_at', 'orders_updated_at_idx');
            },
        );

        Schema::table(
            'prices',
            static function (Blueprint $table) {
                $table->index('product_id', 'prices_product_id_idx');
            },
        );

        Schema::table(
            'products',
            static function (Blueprint $table) {
                $table->index('ext_id', 'products_ext_id_idx');
                $table->index('reserve', 'products_reserve_idx');
                $table->index('stock', 'products_stock_idx');
            },
        );

        Schema::table(
            'products_bundles',
            static function (Blueprint $table) {
                $table->index('ext_id', 'products_bundles_ext_id_idx');
                $table->index('product_id', 'products_bundles_product_id_idx');
                $table->index('bundle_id', 'products_bundles_bundle_id_idx');
            },
        );

        Schema::table(
            'region',
            static function (Blueprint $table) {
                $table->index('ext_id', 'region_ext_id_idx');
            },
        );

        Schema::table(
            'stocks',
            static function (Blueprint $table) {
                $table->index('product_id', 'stocks_product_id_idx');
                $table->index('store_id', 'stocks_store_id_idx');
            },
        );

        Schema::table(
            'stores',
            static function (Blueprint $table) {
                $table->index('ext_id', 'stores_ext_id_idx');
            },
        );
    }

    public function down()
    {
        Schema::table(
            'address_order',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'address_order_order_id_idx',
                        'address_order_region_id_idx',
                    ],
                );
            },
        );

        Schema::table(
            'basket',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'basket_external_id_idx',
                        'basket_order_id_idx',
                        'basket_product_id_idx',
                    ],
                );
            },
        );

        Schema::table(
            'bundles',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'bundles_ext_id_idx',
                    ],
                );
            },
        );

        Schema::table(
            'counterparty',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'counterparty_ext_id_idx',
                        'counterparty_status_id_idx',
                    ],
                );
            },
        );

        Schema::table(
            'counterparty_status',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'counterparty_status_ext_id_idx',
                    ],
                );
            },
        );

        Schema::table(
            'order_status',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'order_status_ext_id_idx',
                        'order_status_name_idx',
                    ],
                );
            },
        );

        Schema::table(
            'orders',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'orders_ext_id_idx',
                        'orders_customer_id_idx',
                        'orders_ext_code_idx',
                        'orders_status_id_idx',
                        'orders_store_id_idx',
                        'orders_updated_at_idx',
                    ],
                );
            },
        );

        Schema::table(
            'prices',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'prices_product_id_idx',
                    ],
                );
            },
        );

        Schema::table(
            'products',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'products_ext_id_idx',
                        'products_reserve_idx',
                        'products_stock_idx',
                    ],
                );
            },
        );

        Schema::table(
            'products_bundles',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'products_bundles_ext_id_idx',
                        'products_bundles_product_id_idx',
                        'products_bundles_bundle_id_idx',
                    ],
                );
            },
        );

        Schema::table(
            'region',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'region_ext_id_idx',
                    ],
                );
            },
        );

        Schema::table(
            'stocks',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'stocks_product_id_idx',
                        'stocks_store_id_idx',
                    ],
                );
            },
        );

        Schema::table(
            'stores',
            static function (Blueprint $table) {
                $table->dropIndex(
                    [
                        'stores_ext_id_idx',
                    ],
                );
            },
        );
    }
}
