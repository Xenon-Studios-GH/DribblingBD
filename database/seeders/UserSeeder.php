<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        User::firstOrCreate(
            ['email' => 'notmunthasir@dribblingbd.com'],
            [
                'name' => 'Munthasir Rahman',
                'email' => 'notmunthasir@dribblingbd.com',
                'phone' => '01700000001',
                'password' => $password,
                'role' => 'superadmin',
                'status' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'irfanmahi@dribblingbd.com'],
            [
                'name' => 'Irfan Mahi',
                'email' => 'irfanmahi@dribblingbd.com',
                'phone' => '01700000002',
                'password' => $password,
                'role' => 'admin',
                'status' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'badshah@dribblingbd.com'],
            [
                'name' => 'Badshah',
                'email' => 'badshah@dribblingbd.com',
                'phone' => '01700000003',
                'password' => $password,
                'role' => 'admin',
                'status' => true,
            ]
        );
    }
}
