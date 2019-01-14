<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Shop;
use App\Blog;
use App\User;

class CreateCommentTable extends Migration
{
    const TABLE_NAME = 'comment';

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
            $table->text('content');

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('content');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $entityShop = Entity::where('name', 'shop')->whereNull('deleted_at')->first();
        $entityBlog = Entity::where('name', 'blog')->whereNull('deleted_at')->first();

        $shop1 = Shop::where('name_en', 'Weinstein Guitars')->whereNull('deleted_at')->first();
        $shop2 = Shop::where('name_en', 'The King Slayer')->whereNull('deleted_at')->first();

        $blog = Blog::where('title_en', 'Soon To Open')->whereNull('deleted_at')->first();

        $user1 = User::where('username', 'grace')->whereNull('deleted_at')->first();
        $user2 = User::where('username', 'joshua')->whereNull('deleted_at')->first();
        $user3 = User::where('username', 'ray')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'entity' => $entityShop->id,
                'entity_id' => $shop1->id,
                'content' => 'Test',
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['entity' => $entityShop->id, 'entity_id' => $shop1->id, 'content' => 'You need to check this place out.',           'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityShop->id, 'entity_id' => $shop1->id, 'content' => 'Hope to have more varieties',                 'created_by' => $user3->id, 'updated_by' => $user3->id],
            ['entity' => $entityShop->id, 'entity_id' => $shop1->id, 'content' => 'There\'s a lot of guitars to choose from...', 'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityShop->id, 'entity_id' => $shop2->id, 'content' => 'Do you have WWE Action Figures?',             'created_by' => $user1->id, 'updated_by' => $user1->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog->id,  'content' => 'Awesome!!',                                   'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog->id,  'content' => 'Nice!!',                                      'created_by' => $user3->id, 'updated_by' => $user3->id],
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
