<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\UserType;

class CreateUserTable extends Migration
{
    const TABLE_NAME = 'user';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->bigInteger('id', 1)->unsigned();
            $table->string('username', 512)->unique();
            $table->string('email', 512)->unique();
            $table->text('salt')->nullable();
            $table->text('password')->nullable();
            $table->string('first_name', 512)->nullable();
            $table->string('middle_name', 512)->nullable();
            $table->string('last_name', 512)->nullable();
            $table->enum('gender', ['m', 'f'])->nullable();
            $table->date('birth_date', 512)->nullable();
            $table->string('mobile_phone', 512)->nullable();
            $table->text('address')->nullable();
            $table->bigInteger('user_type_id')->unsigned();
            $table->string('activation_key', 512)->nullable();

            // Always have these three datetime columns for logs
            $table->timestamp('updated_at');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->timestamp('deleted_at')->nullable();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
        });

        Schema::table(self::TABLE_NAME, function($table) {
            $table->timestamp('created_at')->nullable()->useCurrent()->after('activation_key');
            $table->bigInteger('created_by')->nullable()->unsigned()->after('created_at');
        });

        $userTypeSystemAdministrator = UserType::where('name', 'system administrator')->whereNull('deleted_at')->first();
        $userTypeSystemOperator = UserType::where('name', 'system operator')->whereNull('deleted_at')->first();
        $userTypeRetailer = UserType::where('name', 'retailer')->whereNull('deleted_at')->first();
        $userTypeConsumer = UserType::where('name', 'consumer')->whereNull('deleted_at')->first();
        $userTypeGuest = UserType::where('name', 'guest')->whereNull('deleted_at')->first();

        DB::table(self::TABLE_NAME)->insert([
            ['id' => 13, 'username' => 'jtaylo', 'email' => 'jancarlotaylo@gmail.com', 'salt' => '$2a$12$0a26c90d00f82bf9bb023afed0ce8b4e', 'password' => '$2a$12$0a26c90d00f82bf9bb023ObySkwVf3XBzan4MTvQHtgHvjXCaShcO', 'first_name' => 'JC', 'middle_name' => null, 'last_name' => 'Taylo', 'gender' => 'M', 'birth_date' => null, 'mobile_phone' => '+639954387373', 'address' => null, 'user_type_id' => $userTypeSystemAdministrator->id, 'activation_key' => null, 'created_by' => 13, 'updated_by' => 13],
        ]);

        $tableIncrement = 10000000;
        DB::table(self::TABLE_NAME)->insert([
            [
                'id' => $tableIncrement,
                'username' => 'test',
                'email' => 'test@smail.com',
                'user_type_id' => $userTypeSystemAdministrator->id,
            ],
        ]);

        DB::table(self::TABLE_NAME)->delete([
            ['id' => $tableIncrement],
        ]);

        DB::table(self::TABLE_NAME)->insert([
            ['username' => 'jc',            'email' => 'jancarlotaylo+dev1@gmail.com', 'salt' => '$2a$12$0b2a33d2447441b051f3db53bda76b53', 'password' => '$2a$12$0b2a33d2447441b051f3dOyGh9ORksV7uUVCo0lHBwnf5iOkAot4W', 'first_name' => null,     'middle_name' => null, 'last_name' => null,      'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeRetailer->id,       'activation_key' => null,                               'created_by' => 13, 'updated_by' => 13],
            ['username' => 'jctaylo_dev2',  'email' => 'jancarlotaylo+dev2@gmail.com', 'salt' => '$2a$12$9b175d87318fe393284f4ffb6c6c764f', 'password' => '$2a$12$9b175d87318fe393284f4eYpCmeEa7eJYInR7.l.Yzk9DuarHqZ.e', 'first_name' => null,     'middle_name' => null, 'last_name' => null,      'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeSystemOperator->id, 'activation_key' => null,                               'created_by' => 13, 'updated_by' => 13],
            ['username' => 'karen',         'email' => 'karen@shopu.com',              'salt' => '$2a$12$8481129bce309ce118185059a59d2a2c', 'password' => '$2a$12$8481129bce309ce118185uNgzPAPxqVJaplzGBD2G6PJXTZw3PBAK', 'first_name' => null,     'middle_name' => null, 'last_name' => null,      'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeConsumer->id,       'activation_key' => '2dbd5d93c39a6ca75489b8b99e1910aa', 'created_by' => 13, 'updated_by' => 13],
            ['username' => 'charmaine',     'email' => 'charmaine@shopu.com',          'salt' => '$2a$12$33810ac42588031e7b7a5f88ede6602e', 'password' => '$2a$12$33810ac42588031e7b7a5e1NGLHX8f9WP7UV0sEL27.gS0FKl7A.u', 'first_name' => null,     'middle_name' => null, 'last_name' => null,      'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeConsumer->id,       'activation_key' => 'f02419ee5276dd5cd559bd3f420a4fc2', 'created_by' => 13, 'updated_by' => 13],
            ['username' => 'grace',         'email' => 'grace@shopu.com',              'salt' => '$2a$12$df22f6d4c28a4aa5c790292aa477f3b2', 'password' => '$2a$12$df22f6d4c28a4aa5c7902uRfjWczxcWYBETHlWAZKsfKSweU.UDv2', 'first_name' => null,     'middle_name' => null, 'last_name' => null,      'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeConsumer->id,       'activation_key' => 'f8b30a99aa0e9a6bef8ea8eee6cac1e7', 'created_by' => 13, 'updated_by' => 13],
            ['username' => 'joshua',        'email' => 'joshua@shopu.com',             'salt' => '$2a$12$29fb1171f3e99c382cda1cbdb779a20a', 'password' => '$2a$12$29fb1171f3e99c382cda1OgB47FHmUDcwLkWBRjkkFNYpsDrYo22q', 'first_name' => null,     'middle_name' => null, 'last_name' => null,      'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeConsumer->id,       'activation_key' => 'a129c856cf672862fc0fbef42c693752', 'created_by' => 13, 'updated_by' => 13],
            ['username' => 'ray',           'email' => 'ray@shopu.com',                'salt' => '$2a$12$b6da0fc3fed9264655c4e3e9d30bff84', 'password' => '$2a$12$b6da0fc3fed9264655c4eut77l9GijXl7rRfMr/F6OcXwD4YEpdo2', 'first_name' => null,     'middle_name' => null, 'last_name' => null,      'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeRetailer->id,       'activation_key' => null,                               'created_by' => 13, 'updated_by' => 13],
            ['username' => 'dean',          'email' => 'dean.ambrose@wwe.com',         'salt' => '$2a$12$61c0332e8cb93dddadf9aeeb0ac8e7fd', 'password' => '$2a$12$61c0332e8cb93dddadf9aejtg9pQB2Z6scaZID4./Rd2gPCEH20zq', 'first_name' => 'Dean',   'middle_name' => null, 'last_name' => 'Ambrose', 'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeRetailer->id,       'activation_key' => null,                               'created_by' => 13, 'updated_by' => 13],
            ['username' => 'seth',          'email' => 'seth.rollins@wwe.com',         'salt' => '$2a$12$d8ba31d085e1b4d5ffcbeaf0ecdcaf7d', 'password' => '$2a$12$d8ba31d085e1b4d5ffcbeO.IqJBFPCUOm9Tq3ltebJTA9X3EzqtrS', 'first_name' => 'Seth',   'middle_name' => null, 'last_name' => 'Rollins', 'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeRetailer->id,       'activation_key' => null,                               'created_by' => 13, 'updated_by' => 13],
            ['username' => 'roman',         'email' => 'roman.reigns@wwe.com',         'salt' => '$2a$12$f549509ffe387c63064d360597d02021', 'password' => '$2a$12$f549509ffe387c63064d3uIpFyn/uxgQ./maHxQuC4dBmzEu/m1/K', 'first_name' => 'Roman',  'middle_name' => null, 'last_name' => 'Reigns',  'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeRetailer->id,       'activation_key' => null,                               'created_by' => 13, 'updated_by' => 13],
            ['username' => 'gadget_planet', 'email' => 'gadget.planet@gmail.com',      'salt' => '$2a$12$f549509ffe387c63064d360597d02021', 'password' => '$2a$12$f549509ffe387c63064d3uIpFyn/uxgQ./maHxQuC4dBmzEu/m1/K', 'first_name' => 'Gadget', 'middle_name' => null, 'last_name' => 'Planet',  'gender' => null, 'birth_date' => null, 'mobile_phone' => null,            'address' => null, 'user_type_id' => $userTypeRetailer->id,       'activation_key' => null,                               'created_by' => 13, 'updated_by' => 13],
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
