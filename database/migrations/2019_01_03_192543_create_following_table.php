<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Product;
use App\Image;
use App\Shop;
use App\User;

class CreateFollowingTable extends Migration
{
    const TABLE_NAME = 'following';

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

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('entity_id');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $entityProduct = Entity::where('name', 'product')->whereNull('deleted_at')->first();
        $entityImage = Entity::where('name', 'image')->whereNull('deleted_at')->first();
        $entityShop = Entity::where('name', 'shop')->whereNull('deleted_at')->first();

        $product1 = Product::where('sku', 'IPHONE8')->whereNull('deleted_at')->first();
        $product2 = Product::where('sku', 'IPHONEX')->whereNull('deleted_at')->first();
        $product3 = Product::where('sku', 'ACERVX15')->whereNull('deleted_at')->first();
        $product4 = Product::where('sku', 'GIBSONLESPAUL')->whereNull('deleted_at')->first();
        $product5 = Product::where('sku', 'ZOOMG1X')->whereNull('deleted_at')->first();
        $product6 = Product::where('sku', 'ZOOMB1Xv2')->whereNull('deleted_at')->first();

        $image1 = Image::where('url', 'http://res.cloudinary.com/edgehead17/image/upload/v1543327219/fdoh25ishgedgtfulcon.jpg')->whereNull('deleted_at')->first();
        $image2 = Image::where('url', 'http://res.cloudinary.com/edgehead17/image/upload/v1543327199/yguvldfxsibwcec1cj5h.jpg')->whereNull('deleted_at')->first();
        $image3 = Image::where('url', 'https://res.cloudinary.com/edgehead17/image/upload/v1543327401/wa2tjrr3ifwwfr0uov3b.png')->whereNull('deleted_at')->first();
        $image4 = Image::where('url', 'https://res.cloudinary.com/edgehead17/image/upload/v1543327409/v0qselbbqpta5ucc06th.png')->whereNull('deleted_at')->first();
        $image5 = Image::where('url', 'https://res.cloudinary.com/edgehead17/image/upload/v1543327415/ojycgchodwjjng08576i.png')->whereNull('deleted_at')->first();

        $shop1 = Shop::where('name_en', 'Weinstein Guitars')->whereNull('deleted_at')->first();
        $shop2 = Shop::where('name_en', 'The King Slayer')->whereNull('deleted_at')->first();

        $user1 = User::where('username', 'joshua')->whereNull('deleted_at')->first();
        $user2 = User::where('username', 'ray')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'entity' => $entityProduct->id,
                'entity_id' => $product1->id,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user1->id, 'updated_by' => $user1->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user1->id, 'updated_by' => $user1->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user1->id, 'updated_by' => $user1->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product4->id, 'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product5->id, 'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product6->id, 'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityImage->id,   'entity_id' => $image1->id,   'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityImage->id,   'entity_id' => $image2->id,   'created_by' => $user1->id, 'updated_by' => $user1->id],
            ['entity' => $entityImage->id,   'entity_id' => $image3->id,   'created_by' => $user1->id, 'updated_by' => $user1->id],
            ['entity' => $entityImage->id,   'entity_id' => $image4->id,   'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityImage->id,   'entity_id' => $image5->id,   'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityShop->id,    'entity_id' => $shop1->id,    'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityShop->id,    'entity_id' => $shop1->id,    'created_by' => $user1->id, 'updated_by' => $user1->id],
            ['entity' => $entityShop->id,    'entity_id' => $shop2->id,    'created_by' => $user1->id, 'updated_by' => $user1->id],
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
