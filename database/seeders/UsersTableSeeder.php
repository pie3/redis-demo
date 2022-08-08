<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->times(2)->create();

        // 单独处理第2个用户的数据
        $user = User::find(2);
        $user->name = 'Hys';
        $user->email = 'hys@gmail.com';
        $user->save();
    }
}
