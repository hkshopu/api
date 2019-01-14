<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Color;
use App\Size;

class CreateProductAttributeTable extends Migration
{
    const TABLE_NAME = 'product_attribute';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->bigInteger('size_id')->nullable();
            $table->bigInteger('color_id')->nullable();
            $table->string('other', 512)->nullable();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('other');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $sizeSmall = Size::where('name', 'small')->whereNull('deleted_at')->first();
        $sizeMedium = Size::where('name', 'medium')->whereNull('deleted_at')->first();
        $sizeLarge = Size::where('name', 'large')->whereNull('deleted_at')->first();

        $colorWhite = Color::where('name', 'white')->whereNull('deleted_at')->first();
        $colorGray = Color::where('name', 'gray')->whereNull('deleted_at')->first();
        $colorSilver = Color::where('name', 'silver')->whereNull('deleted_at')->first();
        $colorBlack = Color::where('name', 'black')->whereNull('deleted_at')->first();
        $colorRed = Color::where('name', 'red')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['size_id' => null,            'color_id' => $colorWhite->id,  'other' => null,              'created_by' => 13, 'updated_by' => 13],
            ['size_id' => null,            'color_id' => $colorGray->id,   'other' => null,              'created_by' => 13, 'updated_by' => 13],
            ['size_id' => null,            'color_id' => $colorSilver->id, 'other' => null,              'created_by' => 13, 'updated_by' => 13],
            ['size_id' => $sizeSmall->id,  'color_id' => null,             'other' => null,              'created_by' => 13, 'updated_by' => 13],
            ['size_id' => $sizeMedium->id, 'color_id' => null,             'other' => null,              'created_by' => 13, 'updated_by' => 13],
            ['size_id' => $sizeLarge->id,  'color_id' => null,             'other' => null,              'created_by' => 13, 'updated_by' => 13],
            ['size_id' => null,            'color_id' => $colorSilver->id, 'other' => 'FREE Case',       'created_by' => 13, 'updated_by' => 13],
            ['size_id' => $sizeSmall->id,  'color_id' => $colorWhite->id,  'other' => 'FREE Case',       'created_by' => 13, 'updated_by' => 13],
            ['size_id' => null,            'color_id' => $colorSilver->id, 'other' => 'FREE 10 Bullets', 'created_by' => 13, 'updated_by' => 13],
            ['size_id' => null,            'color_id' => null,             'other' => 'FREE Detonators', 'created_by' => 13, 'updated_by' => 13],
            ['size_id' => $sizeMedium->id, 'color_id' => $colorGray->id,   'other' => 'Buy 1 Take 1',    'created_by' => 13, 'updated_by' => 13],
            ['size_id' => $sizeMedium->id, 'color_id' => $colorGray->id,   'other' => 'Buy 1 Take 2',    'created_by' => 13, 'updated_by' => 13],
            ['size_id' => $sizeMedium->id, 'color_id' => $colorGray->id,   'other' => 'Buy 1 Take 3',    'created_by' => 13, 'updated_by' => 13],
            ['size_id' => null,            'color_id' => $colorBlack->id,   'other' => null,              'created_by' => 13, 'updated_by' => 13],
            ['size_id' => null,            'color_id' => $colorRed->id,  'other' => null,              'created_by' => 13, 'updated_by' => 13],
            ['size_id' => null,            'color_id' => $colorBlack->id,   'other' => 'FREE Case',       'created_by' => 13, 'updated_by' => 13],
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
