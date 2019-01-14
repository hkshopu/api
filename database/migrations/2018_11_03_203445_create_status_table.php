<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatusTable extends Migration
{
    const TABLE_NAME = 'status';

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

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('name');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        DB::table(self::TABLE_NAME)->insert([
            ['name' => 'active', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'disable', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'pause', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'process', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'shipment', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'finish', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'publish', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'draft', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'pending', 'created_by' => 13, 'updated_by' => 13],
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
