<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Check if user with ID 1 exists, if not create it
        if (!User::where('id', 1)->exists()) {
            User::create([
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@mailinator.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@12345678'),
            ]);
        } else {
            User::where('id', 1)->update([
                'email' => 'admin@mailinator.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@12345678'),
            ]);
        }

        $this->call([
            // AppDataSeeder::class,
            EmailTemplateSeeder::class,
        ]);
    }
}
