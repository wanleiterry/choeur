<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user')->insert([
            'role_id' => 1,
            'username' => 'superadmin',
            'nickname' => '超级管理员',
            'password' => bcrypt('secret'),
            'avatar' => '头像地址',
            'email' => 'superadmin@gmail.com',
            'mobile' => '18768132743',
            'qq' => 'QQ号',
            'sex' => 0,
            'address' => '住址',
            'status' => 2,
            'created_by' => 1,
            'updated_by' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}