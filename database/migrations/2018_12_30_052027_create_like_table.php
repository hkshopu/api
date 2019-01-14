<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Entity;
use App\Blog;
use App\User;

class CreateLikeTable extends Migration
{
    const TABLE_NAME = 'like';

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

        $entityBlog = Entity::where('name', 'blog')->whereNull('deleted_at')->first();

        $blog1 = Blog::where('title_en', 'Soon To Open')->whereNull('deleted_at')->first();
        $blog2 = Blog::where('title_en', 'TNA Action Figures PROMO!')->whereNull('deleted_at')->first();
        $blog3 = Blog::where('title_en', 'WWE Action Figures PROMO!')->whereNull('deleted_at')->first();

        $user1 = User::where('username', 'karen')->whereNull('deleted_at')->first();
        $user2 = User::where('username', 'charmaine')->whereNull('deleted_at')->first();
        $user3 = User::where('username', 'grace')->whereNull('deleted_at')->first();
        $user4 = User::where('username', 'joshua')->whereNull('deleted_at')->first();
        $user5 = User::where('username', 'ray')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'entity' => $entityBlog->id,
                'entity_id' => $blog1->id,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['entity' => $entityBlog->id, 'entity_id' => $blog1->id, 'created_by' => $user1->id, 'updated_by' => $user1->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog1->id, 'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog1->id, 'created_by' => $user3->id, 'updated_by' => $user3->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog1->id, 'created_by' => $user4->id, 'updated_by' => $user4->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog1->id, 'created_by' => $user5->id, 'updated_by' => $user5->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog2->id, 'created_by' => $user4->id, 'updated_by' => $user4->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog2->id, 'created_by' => $user5->id, 'updated_by' => $user5->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog3->id, 'created_by' => $user1->id, 'updated_by' => $user1->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog3->id, 'created_by' => $user2->id, 'updated_by' => $user2->id],
            ['entity' => $entityBlog->id, 'entity_id' => $blog3->id, 'created_by' => $user3->id, 'updated_by' => $user3->id],
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
