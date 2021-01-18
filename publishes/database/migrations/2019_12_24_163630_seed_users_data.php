<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

class SeedUsersData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 超级管理员账号
        $userData = [
            'name' => '超级管理员',
            'phone' => '123456',
        ];
         User::insert($userData);

        $user = User::find(1);
        // 授予超级管理员身份
        $user->assignRole('超级管理员');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 回滚数据
        DB::table('users')->truncate();
    }
}
