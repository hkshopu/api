<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Product;
use App\Shop;
use App\Blog;
use App\Category;

class CreateCategoryMapTable extends Migration
{
    const TABLE_NAME = 'category_map';

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
            $table->bigInteger('category_id')->unsigned();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('category_id');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $entityProduct = Entity::where('name', 'product')->whereNull('deleted_at')->first();
        $entityShop = Entity::where('name', 'shop')->whereNull('deleted_at')->first();
        $entityBlog = Entity::where('name', 'blog')->whereNull('deleted_at')->first();

        $productIPHONE8 = Product::where('sku', 'IPHONE8')->whereNull('deleted_at')->first();
        $productIPHONEX = Product::where('sku', 'IPHONEX')->whereNull('deleted_at')->first();
        $productACERVX15 = Product::where('sku', 'ACERVX15')->whereNull('deleted_at')->first();
        $productGIBSONLESPAUL = Product::where('sku', 'GIBSONLESPAUL')->whereNull('deleted_at')->first();
        $productBOSTONOD200 = Product::where('sku', 'BOSTONOD200')->whereNull('deleted_at')->first();
        $productZOOMG1X = Product::where('sku', 'ZOOMG1X')->whereNull('deleted_at')->first();
        $productZOOMB1Xv2 = Product::where('sku', 'ZOOMB1Xv2')->whereNull('deleted_at')->first();
        $productASUSZENFONE3 = Product::where('sku', 'ASUSZENFONE3')->whereNull('deleted_at')->first();
        $productASUSZENFONE4 = Product::where('sku', 'ASUSZENFONE4')->whereNull('deleted_at')->first();
        $productSKU001 = Product::where('sku', 'SKU001')->whereNull('deleted_at')->first();

        $shopVarietyShop = Shop::where('name_en', 'Variety Shop')->whereNull('deleted_at')->first();
        $shopWeinsteinGuitars = Shop::where('name_en', 'Weinstein Guitars')->whereNull('deleted_at')->first();
        $shopTheKingSlayer = Shop::where('name_en', 'The King Slayer')->whereNull('deleted_at')->first();
        $shopTheDirtyDeedsBikeShop = Shop::where('name_en', 'The Dirty Deeds Bike Shop')->whereNull('deleted_at')->first();
        $shopTheRomanEmpire = Shop::where('name_en', 'The Roman Empire')->whereNull('deleted_at')->first();
        $shopGadgetPlanet = Shop::where('name_en', 'Gadget Planet')->whereNull('deleted_at')->first();

        $blog1 = Blog::where('title_en', 'Soon To Open')->whereNull('deleted_at')->first();
        $blog2 = Blog::where('title_en', 'TNA Action Figures PROMO!')->whereNull('deleted_at')->first();
        $blog3 = Blog::where('title_en', 'WWE Action Figures PROMO!')->whereNull('deleted_at')->first();

        $categoryCellphones = Category::where('name', 'Cellphones')->whereNull('deleted_at')->first();
        $categoryLaptops = Category::where('name', 'Laptops')->whereNull('deleted_at')->first();
        $categoryElectronics = Category::where('name', 'Electronics')->whereNull('deleted_at')->first();
        $categoryRetailer = Category::where('name', 'Retailer')->whereNull('deleted_at')->first();
        $categoryGuitarShop = Category::where('name', 'Guitar Shop')->whereNull('deleted_at')->first();
        $categoryBuySell = Category::where('name', 'Buy & Sell')->whereNull('deleted_at')->first();
        $categoryMall = Category::where('name', 'Mall')->whereNull('deleted_at')->first();
        $categoryPromotional = Category::where('name', 'Promotional')->whereNull('deleted_at')->first();
        $categorySale = Category::where('name', 'Sale')->whereNull('deleted_at')->first();
        $categoryAnnouncement = Category::where('name', 'Announcement')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'entity' => $entityProduct->id,
                'entity_id' => $productIPHONE8->id,
                'category_id' => $categoryCellphones->id,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['entity' => $entityProduct->id, 'entity_id' => $productIPHONE8->id,            'category_id' => $categoryCellphones->id,   'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $productIPHONEX->id,            'category_id' => $categoryCellphones->id,   'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $productACERVX15->id,           'category_id' => $categoryLaptops->id,      'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $productGIBSONLESPAUL->id,      'category_id' => $categoryElectronics->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $productBOSTONOD200->id,        'category_id' => $categoryElectronics->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $productZOOMG1X->id,            'category_id' => $categoryElectronics->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $productZOOMB1Xv2->id,          'category_id' => $categoryElectronics->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $productASUSZENFONE3->id,       'category_id' => $categoryCellphones->id,   'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $productASUSZENFONE4->id,       'category_id' => $categoryCellphones->id,   'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $productSKU001->id,             'category_id' => $categoryElectronics->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'entity_id' => $shopVarietyShop->id,           'category_id' => $categoryRetailer->id,     'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'entity_id' => $shopWeinsteinGuitars->id,      'category_id' => $categoryGuitarShop->id,   'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'entity_id' => $shopTheKingSlayer->id,         'category_id' => $categoryBuySell->id,      'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'entity_id' => $shopTheDirtyDeedsBikeShop->id, 'category_id' => $categoryMall->id,         'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'entity_id' => $shopTheRomanEmpire->id,        'category_id' => $categoryMall->id,         'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'entity_id' => $shopGadgetPlanet->id,          'category_id' => $categoryBuySell->id,      'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,    'entity_id' => $blog1->id,                     'category_id' => $categoryPromotional->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,    'entity_id' => $blog2->id,                     'category_id' => $categoryPromotional->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,    'entity_id' => $blog3->id,                     'category_id' => $categorySale->id,         'created_by' => 13, 'updated_by' => 13],
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
