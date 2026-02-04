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
            $table->string('thread_id')->nullable()->after('chat_history_id');
            $table->json('meta')->nullable()->after('content');
        });

        Schema::table('analytics_chat_messages', function (Blueprint $table) {
            $table->string('thread_id')->nullable()->after('chat_history_id');
            $table->json('meta')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['thread_id', 'meta']);
        });

        Schema::table('analytics_chat_messages', function (Blueprint $table) {
            $table->dropColumn(['thread_id', 'meta']);
        });
    }
};
