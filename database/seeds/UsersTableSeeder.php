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
            'updated_at' => new DateTime
        ]);
        DB::table('users')->insertGetId([
            'name' => 'paul',
            'email' => 'paul@gmail.com',
            'password' => Hash::make('paul1234'),
            'profile' => '',
            'type' => 0,
            'phone' => '324356586798',
            'address' => 'Yangon',
            'dob' => '2000-01-01',
            'create_user_id' => 1,
            'updated_user_id' => 1,
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        ]);
        DB::table('users')->insertGetId([
            'name' => 'user 1',
            'email' => 'user1@gmail.com',
            'password' => Hash::make('user11234'),
            'profile' => '',
            'phone' => '324356586798',
            'address' => 'Yangon',
            'dob' => '2000-01-01',
            'create_user_id' => 1,
            'updated_user_id' => 1,
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        ]);
        DB::table('users')->insertGetId([
            'name' => 'user 2',
            'email' => 'user2@gmail.com',
            'password' => Hash::make('user21234'),
            'profile' => '',
            'phone' => '324356586798',
            'address' => 'Yangon',
            'dob' => '2000-01-01',
            'create_user_id' => 2,
            'updated_user_id' => 2,
            'created_at' => new DateTime,
            'updated_at' => new DateTime
        ]);
    }
}
