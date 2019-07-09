<?php

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
        DB::table('users')->insertGetId([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin1234'),
            'profile' => '',
            'type' => 0,
            'phone' => '09123456789',
            'address' => 'Yangon',
            'dob' => '2000-01-01',
            'create_user_id' => 1,
            'updated_user_id' => 1,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
        ]);
    }
}
