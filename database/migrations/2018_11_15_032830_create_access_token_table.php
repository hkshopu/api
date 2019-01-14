<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\User;
use Carbon\Carbon;

class CreateAccessTokenTable extends Migration
{
    const TABLE_NAME = 'access_token';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('token', 512);
            $table->datetime('expires_at');

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('expires_at');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $userSuperAdmin = User::where('username', 'jtaylo')->whereNull('deleted_at')->first();

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'user_id' => $userSuperAdmin->id,
                'token' => 'inferno42',
                'expires_at' => Carbon::now()->addYears(100)->format('Y-m-d H:i:s'),
                'created_by' => 13,
                'updated_by' => 13,
            ],
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
