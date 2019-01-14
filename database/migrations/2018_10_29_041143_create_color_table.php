<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColorTable extends Migration
{
    const TABLE_NAME = 'color';

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
            ['name' => 'white', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'gray', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'silver', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'gold', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'black', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'red', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'light gray', 'created_by' => 13, 'updated_by' => 13],
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
