<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Shop;
use App\User;

class CreateRatingTable extends Migration
{
    const TABLE_NAME = 'rating';

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
            $table->bigInteger('entity_id')->unsigned();
            $table->tinyInteger('rate')->unsigned();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('rate');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $entityShop = Entity::where('name', 'shop')->whereNull('deleted_at')->first();

        $shop1 = Shop::where('name_en', 'Weinstein Guitars')->whereNull('deleted_at')->first();
        $shop2 = Shop::where('name_en', 'The King Slayer')->whereNull('deleted_at')->first();

        $user1 = User::where('username', 'jc')->whereNull('deleted_at')->first();
        $user2 = User::where('username', 'jctaylo_dev2')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'entity' => $entityShop->id,
                'entity_id' => $shop1->id,
                'rate' => 0,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['entity' => $entityShop->id, 'entity_id' => $shop1->id, 'rate' => 2, 'created_by' => $user1->id, 'updated_by' => $user1->id],
            ['entity' => $entityShop->id, 'entity_id' => $shop2->id, 'rate' => 5, 'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityShop->id, 'entity_id' => $shop1->id, 'rate' => 5, 'created_by' => $user2->id, 'updated_by' => $user2->id],
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
