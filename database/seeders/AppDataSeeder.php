<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample DB Config for Admin
        if (!DB::table('database_configurations')->where('name', 'Sample Connection')->exists()) {
            DB::table('database_configurations')->insert([
                'user_id' => 1,
                'name' => 'Sample Connection',
                'connection' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'sample_db',
                'username' => 'root',
                'password' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
