<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user=User::create([
            'name' => 'Test User',
            'email' => 'user@gmail.com',
            'password' => \Hash::make('12345678'),
        ]);
        $user->assignRole('user');
        $admin=User::create([
            'name' => 'Test Admin',
            'email' => 'admin@gmail.com',
            'password' => \Hash::make('12345678'),
        ]);
        $admin->assignRole('admin');
    }
}
