<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@vendorcontract.com',
            'password' => Hash::make('Admin@2024'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Staff',
            'email'    => 'staff@vendorcontract.com',
            'password' => Hash::make('Staff@2024'),
            'role'     => 'staff',
        ]);
    }
}
