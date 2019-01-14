<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Category;

class CreateCategoryLevelTable extends Migration
{
    const TABLE_NAME = 'category_level';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('parent_category_id')->unsigned()->default(0);

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('parent_category_id');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $categoryElectronics = Category::where('name', 'Electronics')->whereNull('deleted_at')->first();
        $categoryComputers = Category::where('name', 'Computers')->whereNull('deleted_at')->first();
        $categoryLaptops = Category::where('name', 'Laptops')->whereNull('deleted_at')->first();
        $categoryGadgets = Category::where('name', 'Gadgets')->whereNull('deleted_at')->first();
        $categoryCellphones = Category::where('name', 'Cellphones')->whereNull('deleted_at')->first();
        $categoryCables = Category::where('name', 'Cables')->whereNull('deleted_at')->first();
        $categoryGuitarShop = Category::where('name', 'Guitar Shop')->whereNull('deleted_at')->first();
        $categoryMall = Category::where('name', 'Mall')->whereNull('deleted_at')->first();
        $categoryApparel = Category::where('name', 'Apparel')->whereNull('deleted_at')->first();
        $categoryAppliances = Category::where('name', 'Appliances')->whereNull('deleted_at')->first();
        $categoryHomeAppliances = Category::where('name', 'Home Appliances')->whereNull('deleted_at')->first();
        $categoryWholesaler = Category::where('name', 'Wholesaler')->whereNull('deleted_at')->first();
        $categoryRetailer = Category::where('name', 'Retailer')->whereNull('deleted_at')->first();
        $categoryWinesBooze = Category::where('name', 'Wines & Booze')->whereNull('deleted_at')->first();
        $categoryBuySell = Category::where('name', 'Buy & Sell')->whereNull('deleted_at')->first();
        $categoryOfficeSupplies = Category::where('name', 'Office Supplies')->whereNull('deleted_at')->first();
        $categoryMusicalInstruments = Category::where('name', 'Musical Instruments')->whereNull('deleted_at')->first();
        $categoryAcousticGuitars = Category::where('name', 'Acoustic Guitars')->whereNull('deleted_at')->first();
        $categoryElectricGuitars = Category::where('name', 'Electric Guitars')->whereNull('deleted_at')->first();
        $categorySURPLUS = Category::where('name', 'SURPLUS')->whereNull('deleted_at')->first();
        $categoryAnnouncement = Category::where('name', 'Announcement')->whereNull('deleted_at')->first();
        $categoryPromotional = Category::where('name', 'Promotional')->whereNull('deleted_at')->first();
        $categorySale = Category::where('name', 'Sale')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'category_id' => $categoryElectronics->id,
                'parent_category_id' => 0,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['category_id' => $categoryElectronics->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryComputers->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryLaptops->id, 'parent_category_id' => $categoryComputers->id, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryGadgets->id, 'parent_category_id' => $categoryElectronics->id, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryCellphones->id, 'parent_category_id' => $categoryGadgets->id, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryCables->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryGuitarShop->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryMall->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryApparel->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryAppliances->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryHomeAppliances->id, 'parent_category_id' => $categoryAppliances->id, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryWholesaler->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryRetailer->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryWinesBooze->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryBuySell->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryOfficeSupplies->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryMusicalInstruments->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryAcousticGuitars->id, 'parent_category_id' => $categoryMusicalInstruments->id, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryElectricGuitars->id, 'parent_category_id' => $categoryMusicalInstruments->id, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categorySURPLUS->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryAnnouncement->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categoryPromotional->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
            ['category_id' => $categorySale->id, 'parent_category_id' => 0, 'created_by' => 13, 'updated_by' => 13],
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
