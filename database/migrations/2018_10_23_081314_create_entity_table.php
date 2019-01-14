<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntityTable extends Migration
{
    const TABLE_NAME = 'entity';

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
            ['name' => 'user', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'category', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'shop', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'product', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'blog', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'image', 'created_by' => 13, 'updated_by' => 13],
            ['name' => 'comment', 'created_by' => 13, 'updated_by' => 13],
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
