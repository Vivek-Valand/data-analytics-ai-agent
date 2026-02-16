<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure user 1 exists before adding FK or updating records
        if (!DB::table('users')->where('id', 1)->exists()) {
            DB::table('users')->insert([
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@mailinator.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@12345678'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!Schema::hasColumn('database_configurations', 'user_id')) {
            Schema::table('database_configurations', function (Blueprint $table) {
                $table->foreignId('user_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('sql_file_configs', 'user_id')) {
            Schema::table('sql_file_configs', function (Blueprint $table) {
                $table->foreignId('user_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            });
        }

        // Assign existing data to user_id = 1
        DB::table('database_configurations')->whereNull('user_id')->update(['user_id' => 1]);
        DB::table('sql_file_configs')->whereNull('user_id')->update(['user_id' => 1]);
        
        // analytics_chat_histories already has user_id, but some might be null or 0
        if (Schema::hasColumn('analytics_chat_histories', 'user_id')) {
             DB::table('analytics_chat_histories')->where('user_id', 0)->orWhereNull('user_id')->update(['user_id' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('database_configurations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('sql_file_configs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
