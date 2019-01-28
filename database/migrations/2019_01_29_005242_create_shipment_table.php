<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentTable extends Migration
{
    const TABLE_NAME = 'shipment';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->string('name', 512)->unique();
            $table->string('label', 512);

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('label');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        DB::table(self::TABLE_NAME)->insert([
            ['name' => 'normal', 'label' => 'Normal', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'all', 'label' => 'All order fee shipment', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'over', 'label' => 'Fee shipment, If Order total price over:', 'created_by' => 13, 'updated_by' => 13],
        ]);
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
