<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->longText('content')->nullable()->change();
        });

        Schema::table('analytics_chat_messages', function (Blueprint $table) {
            $table->text('content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->longText('content')->nullable(false)->change();
        });

        Schema::table('analytics_chat_messages', function (Blueprint $table) {
            $table->text('content')->nullable(false)->change();
        });
    }
};
