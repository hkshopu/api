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
            $table->mediumInteger('id', 1)->unsinged();
            $table->string('name', 128)->unique();

            // Always have these three datetime columns for logs
            $table->nullableTimestamps();
            $table->softDeletes();
            // $table->timestamp('created_at');
            $table->integer('created_by')->nullable();
            // $table->datetime('updated_at');
            $table->integer('updated_by')->nullable();
            // $table->datetime('deleted_at');
            $table->integer('deleted_by')->nullable();
        });

        // DB::table(self::TABLE_NAME)->insert([
        //     ['name' => 'user', 'created_by' => 1],
        //     ['name' => 'category', 'created_by' => 1],
        //     ['name' => 'shop', 'created_by' => 1],
        //     ['name' => 'product', 'created_by' => 1],
        //     ['name' => 'blog', 'created_by' => 1],
        //     ['name' => 'image', 'created_by' => 1],
        //     ['name' => 'comment', 'created_by' => 1],
        // ]);
        exit;
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
