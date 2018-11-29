<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTable extends Migration
{
    const TABLE_NAME = 'product';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku');
            $table->string('name_en');
            $table->string('name_tc');
            $table->string('name_sc');
            $table->text('description_en');
            $table->text('description_tc');
            $table->text('description_sc');
            
            // Always have these three datetime columns for logs
            $table->timestamp('created_at');
            $table->integer('created_by');
            $table->datetime('updated_at');
            $table->integer('updated_by');
            $table->datetime('deleted_at');
            $table->integer('deleted_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}
