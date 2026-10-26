<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@inventory.test'],
            [
                'name' => 'Demo Admin',
                'password' => 'admin12345',
                'role' => 'admin',
            ],
        );

        User::updateOrCreate(
            ['email' => 'staff@inventory.test'],
            [
                'name' => 'Demo Staff',
                'password' => 'staff12345',
                'role' => 'staff',
            ],
        );

        User::updateOrCreate(
            ['email' => 'customer@inventory.test'],
            [
                'name' => 'Demo Customer',
                'password' => 'customer12345',
                'role' => 'customer',
            ],
        );

        $this->call(PaymentChannelSeeder::class);
    }
}
