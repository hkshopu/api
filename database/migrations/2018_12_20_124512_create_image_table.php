<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Product;
use App\Shop;
use App\User;

class CreateImageTable extends Migration
{
    const TABLE_NAME = 'image';

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
            $table->text('url');
            $table->enum('type', ['primary'])->default('primary');
            $table->tinyInteger('sort')->unsigned()->default(0);

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('sort');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $entityProduct = Entity::where('name', 'product')->whereNull('deleted_at')->first();
        $entityShop = Entity::where('name', 'shop')->whereNull('deleted_at')->first();
        $entityBlog = Entity::where('name', 'blog')->whereNull('deleted_at')->first();
        $entityUser = Entity::where('name', 'user')->whereNull('deleted_at')->first();

        $product1 = Product::where('sku', 'IPHONE8')->whereNull('deleted_at')->first();
        $product2 = Product::where('sku', 'IPHONEX')->whereNull('deleted_at')->first();
        $product3 = Product::where('sku', 'ACERVX15')->whereNull('deleted_at')->first();
        $product4 = Product::where('sku', 'GIBSONLESPAUL')->whereNull('deleted_at')->first();
        $product5 = Product::where('sku', 'BOSTONOD200')->whereNull('deleted_at')->first();
        $product6 = Product::where('sku', 'ZOOMG1X')->whereNull('deleted_at')->first();
        $product7 = Product::where('sku', 'ZOOMB1Xv2')->whereNull('deleted_at')->first();

        $shop = Shop::where('name_en', 'Weinstein Guitars')->whereNull('deleted_at')->first();

        $user = User::where('username', 'seth')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'entity' => $entityProduct->id,
                'entity_id' => $product1->id,
                'url' => 'image.jpg',
                'type' => 'primary',
                'sort' => 0,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'url' => 'http://res.cloudinary.com/edgehead17/image/upload/v1543327219/fdoh25ishgedgtfulcon.jpg',  'type' => 'primary', 'sort' => 1, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'url' => 'http://res.cloudinary.com/edgehead17/image/upload/v1543327199/yguvldfxsibwcec1cj5h.jpg',  'type' => 'primary', 'sort' => 1, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'url' => 'http://res.cloudinary.com/edgehead17/image/upload/v1543339888/k0lydamsys7ns1jm4zo3.jpg',  'type' => 'primary', 'sort' => 2, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327401/wa2tjrr3ifwwfr0uov3b.png', 'type' => 'primary', 'sort' => 1, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327409/v0qselbbqpta5ucc06th.png', 'type' => 'primary', 'sort' => 2, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327415/ojycgchodwjjng08576i.png', 'type' => 'primary', 'sort' => 3, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327427/kfgygdto19z6tlhpjoxd.png', 'type' => 'primary', 'sort' => 4, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327434/spbait0nl2folsleh7db.png', 'type' => 'primary', 'sort' => 5, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327440/reg3wdizsvzbalubrv2j.png', 'type' => 'primary', 'sort' => 6, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327446/waihghuqmpbzuvjn1fht.png', 'type' => 'primary', 'sort' => 7, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327453/vo0kvgzv1tmu8xhioult.png', 'type' => 'primary', 'sort' => 8, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product4->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327544/xclxdw8vnoppjxcjoqf2.jpg', 'type' => 'primary', 'sort' => 1, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product4->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327564/ul0pnw70qiabhelmezcj.jpg', 'type' => 'primary', 'sort' => 2, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product4->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327579/c6rvo8zinn2gnc2agwpt.jpg', 'type' => 'primary', 'sort' => 3, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product4->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327589/mzgdyzcphq5rkptxtuls.jpg', 'type' => 'primary', 'sort' => 4, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product4->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327600/mk8fvb8i99d5bgap5ddi.jpg', 'type' => 'primary', 'sort' => 5, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product4->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327613/ddofth1hpjguam8drjtn.jpg', 'type' => 'primary', 'sort' => 6, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product4->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327635/jwbefli1uqu7athyvzbs.jpg', 'type' => 'primary', 'sort' => 7, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product4->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327635/jwbefli1uqu7athyvzbs.jpg', 'type' => 'primary', 'sort' => 8, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product5->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327483/zoqzjky3qoudo3p0isgo.jpg', 'type' => 'primary', 'sort' => 1, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product6->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327502/gxsprphownnkrnyjan5m.jpg', 'type' => 'primary', 'sort' => 1, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityProduct->id, 'entity_id' => $product7->id, 'url' => 'https://res.cloudinary.com/edgehead17/image/upload/v1543327474/td7jbxrrqp161kffes0o.jpg', 'type' => 'primary', 'sort' => 1, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'entity_id' => $shop->id,     'url' => 'http://res.cloudinary.com/edgehead17/image/upload/v1543762616/gnodlppxioau0kbxai16.jpg',  'type' => 'primary', 'sort' => 1, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityShop->id,    'entity_id' => $shop->id,     'url' => 'http://res.cloudinary.com/edgehead17/image/upload/v1543762654/mwtnsesffjfojyxeoytg.jpg',  'type' => 'primary', 'sort' => 2, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityBlog->id,    'entity_id' => $shop->id,     'url' => 'http://res.cloudinary.com/edgehead17/image/upload/v1544335216/ftvvgzagoct0b6c20q7r.png',  'type' => 'primary', 'sort' => 1, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,    'entity_id' => $user->id,     'url' => 'http://res.cloudinary.com/edgehead17/image/upload/v1546970845/pik3xsbdfzas2pid6uah.jpg',  'type' => 'primary', 'sort' => 1, 'created_by' => 13, 'updated_by' => 13],
            ['entity' => $entityUser->id,    'entity_id' => $user->id,     'url' => 'http://res.cloudinary.com/edgehead17/image/upload/v1546970862/d32cdb9pdgdnwc1kyppb.jpg',  'type' => 'primary', 'sort' => 2, 'created_by' => 13, 'updated_by' => 13],
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
