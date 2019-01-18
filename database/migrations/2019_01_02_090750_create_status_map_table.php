<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Product;
use App\Category;
use App\Shop;
use App\Comment;
use App\Blog;
use App\User;
use App\Status;

class CreateStatusMapTable extends Migration
{
    const TABLE_NAME = 'status_map';

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
        $entityCategory = Entity::where('name', 'category')->whereNull('deleted_at')->first();
        $entityShop = Entity::where('name', 'shop')->whereNull('deleted_at')->first();
        $entityComment = Entity::where('name', 'comment')->whereNull('deleted_at')->first();
        $entityBlog = Entity::where('name', 'blog')->whereNull('deleted_at')->first();
        $entityUser = Entity::where('name', 'user')->whereNull('deleted_at')->first();

        $product1 = Product::where('sku', 'IPHONE8')->whereNull('deleted_at')->first();
        $product2 = Product::where('sku', 'IPHONEX')->whereNull('deleted_at')->first();
        $product3 = Product::where('sku', 'ACERVX15')->whereNull('deleted_at')->first();
        $product4 = Product::where('sku', 'GIBSONLESPAUL')->whereNull('deleted_at')->first();
        $product5 = Product::where('sku', 'BOSTONOD200')->whereNull('deleted_at')->first();
        $product6 = Product::where('sku', 'ZOOMG1X')->whereNull('deleted_at')->first();
        $product7 = Product::where('sku', 'ZOOMB1Xv2')->whereNull('deleted_at')->first();
        $product8 = Product::where('sku', 'ASUSZENFONE3')->whereNull('deleted_at')->first();
        $product9 = Product::where('sku', 'ASUSZENFONE4')->whereNull('deleted_at')->first();
        $product10 = Product::where('sku', 'SKU001')->whereNull('deleted_at')->first();

        $category1 = Category::where('name', 'Electronics')->whereNull('deleted_at')->first();
        $category2 = Category::where('name', 'Computers')->whereNull('deleted_at')->first();
        $category3 = Category::where('name', 'Laptops')->whereNull('deleted_at')->first();
        $category4 = Category::where('name', 'Gadgets')->whereNull('deleted_at')->first();
        $category5 = Category::where('name', 'Cellphones')->whereNull('deleted_at')->first();
        $category6 = Category::where('name', 'Cables')->whereNull('deleted_at')->first();
        $category7 = Category::where('name', 'Guitar Shop')->whereNull('deleted_at')->first();
        $category8 = Category::where('name', 'Mall')->whereNull('deleted_at')->first();
        $category9 = Category::where('name', 'Apparel')->whereNull('deleted_at')->first();
        $category10 = Category::where('name', 'Appliances')->whereNull('deleted_at')->first();
        $category11 = Category::where('name', 'Home Appliances')->whereNull('deleted_at')->first();
        $category12 = Category::where('name', 'Wholesaler')->whereNull('deleted_at')->first();
        $category13 = Category::where('name', 'Retailer')->whereNull('deleted_at')->first();
        $category14 = Category::where('name', 'Wines & Booze')->whereNull('deleted_at')->first();
        $category15 = Category::where('name', 'Buy & Sell')->whereNull('deleted_at')->first();
        $category16 = Category::where('name', 'Office Supplies')->whereNull('deleted_at')->first();
        $category17 = Category::where('name', 'Musical Instruments')->whereNull('deleted_at')->first();
        $category18 = Category::where('name', 'Acoustic Guitars')->whereNull('deleted_at')->first();
        $category19 = Category::where('name', 'Electric Guitars')->whereNull('deleted_at')->first();
        $category20 = Category::where('name', 'SURPLUS')->whereNull('deleted_at')->first();
        $category21 = Category::where('name', 'Announcement')->whereNull('deleted_at')->first();
        $category22 = Category::where('name', 'Promotional')->whereNull('deleted_at')->first();
        $category23 = Category::where('name', 'Sale')->whereNull('deleted_at')->first();

        $shop1 = Shop::where('name_en', 'Variety Shop')->whereNull('deleted_at')->first();
        $shop2 = Shop::where('name_en', 'Weinstein Guitars')->whereNull('deleted_at')->first();
        $shop3 = Shop::where('name_en', 'The King Slayer')->whereNull('deleted_at')->first();
        $shop4 = Shop::where('name_en', 'The Dirty Deeds Bike Shop')->whereNull('deleted_at')->first();
        $shop5 = Shop::where('name_en', 'The Roman Empire')->whereNull('deleted_at')->first();
        $shop6 = Shop::where('name_en', 'Gadget Planet')->whereNull('deleted_at')->first();

        $comment1 = Comment::where('content', 'Hope to have more varieties')->whereNull('deleted_at')->first();
        $comment2 = Comment::where('content', 'You need to check this place out.')->whereNull('deleted_at')->first();
        $comment3 = Comment::where('content', 'There\'s a lot of guitars to choose from...')->whereNull('deleted_at')->first();
        $comment4 = Comment::where('content', 'Do you have WWE Action Figures?')->whereNull('deleted_at')->first();
        $comment5 = Comment::where('content', 'Awesome!!')->whereNull('deleted_at')->first();
        $comment6 = Comment::where('content', 'Nice!!')->whereNull('deleted_at')->first();

        $blog1 = Blog::where('title_en', 'Soon To Open')->whereNull('deleted_at')->first();
        $blog2 = Blog::where('title_en', 'TNA Action Figures PROMO!')->whereNull('deleted_at')->first();
        $blog3 = Blog::where('title_en', 'WWE Action Figures PROMO!')->whereNull('deleted_at')->first();

        $user1 = User::where('username', 'jtaylo')->whereNull('deleted_at')->first();
        $user2 = User::where('username', 'jc')->whereNull('deleted_at')->first();
        $user3 = User::where('username', 'jctaylo_dev2')->whereNull('deleted_at')->first();
        $user4 = User::where('username', 'karen')->whereNull('deleted_at')->first();
        $user5 = User::where('username', 'charmaine')->whereNull('deleted_at')->first();
        $user6 = User::where('username', 'grace')->whereNull('deleted_at')->first();
        $user7 = User::where('username', 'joshua')->whereNull('deleted_at')->first();
        $user8 = User::where('username', 'ray')->whereNull('deleted_at')->first();
        $user9 = User::where('username', 'dean')->whereNull('deleted_at')->first();
        $user10 = User::where('username', 'seth')->whereNull('deleted_at')->first();
        $user11 = User::where('username', 'roman')->whereNull('deleted_at')->first();
        $user12 = User::where('username', 'gadget_planet')->whereNull('deleted_at')->first();

        $statusActive = Status::where('name', 'active')->whereNull('deleted_at')->first();
        $statusDisable = Status::where('name', 'disable')->whereNull('deleted_at')->first();
        $statusFinish = Status::where('name', 'finish')->whereNull('deleted_at')->first();
        $statusPublish = Status::where('name', 'publish')->whereNull('deleted_at')->first();
        $statusDraft = Status::where('name', 'draft')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'entity' => $entityProduct->id,
                'entity_id' => $product1->id,
                'status_id' => $statusActive->id,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['entity' => $entityProduct->id,  'entity_id' => $product1->id,   'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'entity_id' => $product2->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'entity_id' => $product3->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'entity_id' => $product4->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'entity_id' => $product5->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'entity_id' => $product6->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'entity_id' => $product7->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'entity_id' => $product8->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'entity_id' => $product9->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id,  'entity_id' => $product10->id,  'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category1->id,  'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category2->id,  'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category3->id,  'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category4->id,  'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category5->id,  'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category6->id,  'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category7->id,  'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category8->id,  'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category9->id,  'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category10->id, 'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category11->id, 'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category12->id, 'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category13->id, 'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category14->id, 'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category15->id, 'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category16->id, 'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category17->id, 'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category18->id, 'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category19->id, 'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category20->id, 'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category21->id, 'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category22->id, 'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityCategory->id, 'entity_id' => $category23->id, 'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,     'entity_id' => $shop1->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,     'entity_id' => $shop2->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,     'entity_id' => $shop3->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,     'entity_id' => $shop4->id,      'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,     'entity_id' => $shop5->id,      'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,     'entity_id' => $shop6->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityComment->id,  'entity_id' => $comment1->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityComment->id,  'entity_id' => $comment2->id,   'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityComment->id,  'entity_id' => $comment3->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityComment->id,  'entity_id' => $comment4->id,   'status_id' => $statusDisable->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityComment->id,  'entity_id' => $comment5->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityComment->id,  'entity_id' => $comment6->id,   'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,     'entity_id' => $blog1->id,      'status_id' => $statusFinish->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,     'entity_id' => $blog2->id,      'status_id' => $statusDraft->id,   'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,     'entity_id' => $blog3->id,      'status_id' => $statusPublish->id, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user1->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user2->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user3->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user4->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user5->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user6->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user7->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user8->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user9->id,      'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user10->id,     'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user11->id,     'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,     'entity_id' => $user12->id,     'status_id' => $statusActive->id,  'created_by' => 13, 'updated_by' => 13],
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
