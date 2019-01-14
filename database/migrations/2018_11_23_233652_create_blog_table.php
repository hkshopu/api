<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Shop;
use App\User;

class CreateBlogTable extends Migration
{
    const TABLE_NAME = 'blog';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->string('title_en', 512);
            $table->string('title_tc', 512)->nullable();
            $table->string('title_sc', 512)->nullable();
            $table->text('content_en');
            $table->text('content_tc')->nullable();
            $table->text('content_sc')->nullable();
            $table->tinyInteger('is_top')->default(0);
            $table->bigInteger('shop_id')->unsigned();
            $table->datetime('date_publish_start')->nullable();
            $table->datetime('date_publish_end')->nullable();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('date_publish_end');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $shop = Shop::where('name_en', 'The King Slayer')->whereNull('deleted_at')->first();
        $user = User::where('username', 'seth')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'title_en' => 'Test',
                'content_en' => 'Test',
                'shop_id' => $shop->id,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['title_en' => 'Soon To Open'             , 'title_tc' => '很快就要開了'      , 'title_sc' => '很快就要开了'       , 'content_en' => 'We are happy to announce the opening of our toy collections and action figures shop!', 'content_tc' => '我們很高興地宣布開設我們的玩具收藏品和動作人物店！', 'content_sc' => '我们很高兴地宣布开设我们的玩具收藏品和动作人物店！', 'is_top' => 0, 'shop_id' => $shop->id,          'date_publish_start' => null, 'date_publish_end' => null,                                   'created_by' => $user->id, 'updated_by' => $user->id],
            ['title_en' => 'TNA Action Figures PROMO!', 'title_tc' => 'TNA行動人物PROMO！', 'title_sc' => 'TNA行动人物PROMO！' , 'content_en' => 'Check out for news update.'                                                          , 'content_tc' => 'Chákàn xīnwén gēngxīn.'                            , 'content_sc' => '查看新闻更新。'                            , 'is_top' => 0        , 'shop_id' => $shop->id,          'date_publish_start' => null, 'date_publish_end' => null,                                   'created_by' => $user->id, 'updated_by' => $user->id],
            ['title_en' => 'WWE Action Figures PROMO!', 'title_tc' => 'WWE行動人物宣傳！' , 'title_sc' => 'WWE行动人物宣传！'  , 'content_en' => 'Check out for news update.'                                                          , 'content_tc' => 'Chákàn xīnwén gēngxīn.'                            , 'content_sc' => '查看新闻更新。'                             , 'is_top' => 0       , 'shop_id' => $shop->id,          'date_publish_start' => '2018-01-01 00:00:00', 'date_publish_end' => '2020-01-01 00:00:00', 'created_by' => $user->id, 'updated_by' => $user->id],
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
