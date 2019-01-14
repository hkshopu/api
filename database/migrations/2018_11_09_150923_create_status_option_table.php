<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Status;

class CreateStatusOptionTable extends Migration
{
    const TABLE_NAME = 'status_option';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->bigInteger('entity')->unsigned();
            $table->bigInteger('status_id')->unsigned();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('status_id');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $entityProduct = Entity::where('name', 'product')->whereNull('deleted_at')->first();
        $entityImage = Entity::where('name', 'image')->whereNull('deleted_at')->first();
        $entityCategory = Entity::where('name', 'category')->whereNull('deleted_at')->first();
        $entityShop = Entity::where('name', 'shop')->whereNull('deleted_at')->first();
        $entityComment = Entity::where('name', 'comment')->whereNull('deleted_at')->first();
        $entityBlog = Entity::where('name', 'blog')->whereNull('deleted_at')->first();
        $entityUser = Entity::where('name', 'user')->whereNull('deleted_at')->first();

        $statusActive = Status::where('name', 'active')->whereNull('deleted_at')->first();
        $statusDisable = Status::where('name', 'disable')->whereNull('deleted_at')->first();
        $statusPause = Status::where('name', 'pause')->whereNull('deleted_at')->first();
        $statusProcess = Status::where('name', 'process')->whereNull('deleted_at')->first();
        $statusShipment = Status::where('name', 'shipment')->whereNull('deleted_at')->first();
        $statusFinish = Status::where('name', 'finish')->whereNull('deleted_at')->first();
        $statusPublish = Status::where('name', 'publish')->whereNull('deleted_at')->first();
        $statusDraft = Status::where('name', 'draft')->whereNull('deleted_at')->first();
        $statusPending = Status::where('name', 'pending')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'entity' => $entityProduct->id,
                'status_id' => $statusActive->id,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['entity' => $entityProduct->id,  'status_id' => $statusActive->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'status_id' => $statusPause->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'status_id' => $statusActive->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,     'status_id' => $statusActive->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,     'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityComment->id,  'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityComment->id,  'status_id' => $statusActive->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,     'status_id' => $statusPublish->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,     'status_id' => $statusDraft->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,     'status_id' => $statusFinish->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'status_id' => $statusActive->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
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
