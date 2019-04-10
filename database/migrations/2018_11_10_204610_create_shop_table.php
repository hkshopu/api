<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\User;

class CreateShopTable extends Migration
{
    const TABLE_NAME = 'shop';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->string('name_en', 512)->unique();
            $table->string('name_tc', 512)->nullable();
            $table->string('name_sc', 512)->nullable();
            $table->text('description_en');
            $table->text('description_tc')->nullable();
            $table->text('description_sc')->nullable();
            $table->bigInteger('user_id')->nullable()->unsigned();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('user_id');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $userRetailer1 = User::where('username', 'ray')->whereNull('deleted_at')->first();
        $userRetailer2 = User::where('username', 'jc')->whereNull('deleted_at')->first();
        $userRetailer3 = User::where('username', 'seth')->whereNull('deleted_at')->first();
        $userRetailer4 = User::where('username', 'dean')->whereNull('deleted_at')->first();
        $userRetailer5 = User::where('username', 'roman')->whereNull('deleted_at')->first();
        $userRetailer6 = User::where('username', 'gadget_planet')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'name_en' => 'Test',
                'description_en' => 'Test',
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['name_en' => 'Variety Shop',              'name_tc' => null,                    'name_sc' => null, 'description_en' => 'Variety Shop.',                                              'description_tc' => null, 'description_sc' => null, 'user_id' => $userRetailer1->id, 'created_by' => 13, 'updated_by' => 13],
            ['name_en' => 'Weinstein Guitars',         'name_tc' => null,                    'name_sc' => null, 'description_en' => 'Weinstein Pianos & Guitars.',                                'description_tc' => null, 'description_sc' => null, 'user_id' => $userRetailer2->id, 'created_by' => 13, 'updated_by' => 13],
            ['name_en' => 'The King Slayer',           'name_tc' => 'Mattel Action Figures', 'name_sc' => null, 'description_en' => 'Collectors toys and action figures for buying and selling.', 'description_tc' => null, 'description_sc' => null, 'user_id' => $userRetailer3->id, 'created_by' => 13, 'updated_by' => 13],
            ['name_en' => 'The Dirty Deeds Bike Shop', 'name_tc' => null,                    'name_sc' => null, 'description_en' => '',                                                           'description_tc' => null, 'description_sc' => null, 'user_id' => $userRetailer4->id, 'created_by' => 13, 'updated_by' => 13],
            ['name_en' => 'The Roman Empire',          'name_tc' => null,                    'name_sc' => null, 'description_en' => '',                                                           'description_tc' => null, 'description_sc' => null, 'user_id' => $userRetailer5->id, 'created_by' => 13, 'updated_by' => 13],
            ['name_en' => 'Gadget Planet',             'name_tc' => null,                    'name_sc' => null, 'description_en' => '',                                                           'description_tc' => null, 'description_sc' => null, 'user_id' => $userRetailer6->id, 'created_by' => 13, 'updated_by' => 13],
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
