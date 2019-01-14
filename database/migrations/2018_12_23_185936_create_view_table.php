<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Product;
use App\Blog;
use App\User;

class CreateViewTable extends Migration
{
    const TABLE_NAME = 'view';

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
        $entityBlog = Entity::where('name', 'blog')->whereNull('deleted_at')->first();

        $product1 = Product::where('sku', 'IPHONE8')->whereNull('deleted_at')->first();
        $product2 = Product::where('sku', 'IPHONEX')->whereNull('deleted_at')->first();
        $product3 = Product::where('sku', 'BOSTONOD200')->whereNull('deleted_at')->first();

        $blog = Blog::where('title_en', 'Soon To Open')->whereNull('deleted_at')->first();

        $user1 = User::where('username', 'jc')->whereNull('deleted_at')->first();
        $user2 = User::where('username', 'jctaylo_dev2')->whereNull('deleted_at')->first();
        $user3 = User::where('username', 'karen')->whereNull('deleted_at')->first();
        $user4 = User::where('username', 'charmaine')->whereNull('deleted_at')->first();
        $user5 = User::where('username', 'grace')->whereNull('deleted_at')->first();
        $user6 = User::where('username', 'joshua')->whereNull('deleted_at')->first();
        $user7 = User::where('username', 'ray')->whereNull('deleted_at')->first();
        $user8 = User::where('username', 'dean')->whereNull('deleted_at')->first();
        $user9 = User::where('username', 'seth')->whereNull('deleted_at')->first();
        $user10 = User::where('username', 'roman')->whereNull('deleted_at')->first();

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
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user1->id,  'updated_by' => $user1->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user2->id,  'updated_by' => $user2->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user3->id,  'updated_by' => $user3->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user4->id,  'updated_by' => $user4->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user5->id,  'updated_by' => $user5->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user6->id,  'updated_by' => $user6->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user7->id,  'updated_by' => $user7->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user8->id,  'updated_by' => $user8->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user9->id,  'updated_by' => $user9->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product1->id, 'created_by' => $user10->id, 'updated_by' => $user10->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user1->id,  'updated_by' => $user1->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user2->id,  'updated_by' => $user2->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user3->id,  'updated_by' => $user3->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user4->id,  'updated_by' => $user4->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user5->id,  'updated_by' => $user5->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user6->id,  'updated_by' => $user6->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user7->id,  'updated_by' => $user7->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user8->id,  'updated_by' => $user8->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user9->id,  'updated_by' => $user9->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product2->id, 'created_by' => $user10->id, 'updated_by' => $user10->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user1->id,  'updated_by' => $user1->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user2->id,  'updated_by' => $user2->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user3->id,  'updated_by' => $user3->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user4->id,  'updated_by' => $user4->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user5->id,  'updated_by' => $user5->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user6->id,  'updated_by' => $user6->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user7->id,  'updated_by' => $user7->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user8->id,  'updated_by' => $user8->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user9->id,  'updated_by' => $user9->id],
            ['entity' => $entityProduct->id, 'entity_id' => $product3->id, 'created_by' => $user10->id, 'updated_by' => $user10->id],
            ['entity' => $entityBlog->id,    'entity_id' => $blog->id,     'created_by' => $user4->id,  'updated_by' => $user4->id],
            ['entity' => $entityBlog->id,    'entity_id' => $blog->id,     'created_by' => $user5->id,  'updated_by' => $user5->id],
            ['entity' => $entityBlog->id,    'entity_id' => $blog->id,     'created_by' => $user6->id,  'updated_by' => $user6->id],
            ['entity' => $entityBlog->id,    'entity_id' => $blog->id,     'created_by' => $user7->id,  'updated_by' => $user7->id],
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
