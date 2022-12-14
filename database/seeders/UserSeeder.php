<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    User,
    Role,
    Permission
};

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            ['name' => 'user', 'email' => 'user@test.com', 'password' => bcrypt('password')],
            ['name' => 'Editor', 'email' => 'editor@test.com', 'password' => bcrypt('password')],
            ['name' => 'Author', 'email' => 'author@test.com', 'password' => bcrypt('password')],
        ]);

        Role::insert([
            ['name'=>'Editor','slug'=>'editor'],
            ['name'=>'Author','slug'=>'author'],
        ]);

        Permission::insert([
            ['name'=>'Add Post','slug'=>'add-post'],
            ['name'=>'Delete Post','slug'=>'delete-post'],
        ]);
    }
}
