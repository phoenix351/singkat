<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@bps.go.id',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Kevin Mclaren Pandoh',
                'username' => 'kevinmpandoh',
                'email' => 'kevinmpandoh@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'operator',
            ],
            [
                'name' => 'Mclaren',
                'username' => 'mclaren',
                'email' => 'mclaren@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'viewer',
            ],
        ];

        foreach ($users as $user) {
            \App\Models\User::create($user);
        }
    }
}
