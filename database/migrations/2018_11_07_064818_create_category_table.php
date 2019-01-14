<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;

class CreateCategoryTable extends Migration
{
    const TABLE_NAME = 'category';

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

        $entityProduct = Entity::where('name', 'product')->whereNull('deleted_at')->first();
        $entityShop = Entity::where('name', 'shop')->whereNull('deleted_at')->first();
        $entityBlog = Entity::where('name', 'blog')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'entity' => $entityProduct->id,
                'name' => 'Test',
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['entity' => $entityProduct->id, 'name' => 'Electronics', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Computers', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Laptops', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Gadgets', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Cellphones', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Cables', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Apparel', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Appliances', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Home Appliances', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Office Supplies', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Musical Instruments', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Acoustic Guitars', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'name' => 'Electric Guitars', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'name' => 'Guitar Shop', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'name' => 'Mall', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'name' => 'Wholesaler', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'name' => 'Retailer', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'name' => 'Wines & Booze', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'name' => 'Buy & Sell', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'name' => 'SURPLUS', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,    'name' => 'Announcement', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,    'name' => 'Promotional', 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,    'name' => 'Sale', 'created_by' => 13, 'updated_by' => 13],
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
