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
        $histories = \DB::table('chat_histories')->get();
        foreach ($histories as $history) {
            $newHistoryId = \DB::table('analytics_chat_histories')->insertGetId([
                'user_id' => $history->user_id ?? 1,
                'title' => $history->title,
                'created_at' => $history->created_at,
                'updated_at' => $history->updated_at,
            ]);

            $messages = \DB::table('chat_messages')->where('chat_history_id', $history->id)->get();
            foreach ($messages as $message) {
                \DB::table('analytics_chat_messages')->insert([
                    'chat_history_id' => $newHistoryId,
                    'role' => $message->role,
                    'content' => $message->content,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                ]);
            }
        }

        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_histories');
    }

    public function down(): void
    {
        // No easy way to undo this without a backup of the original tables structure
        // But we can recreate the tables at least
        Schema::create('chat_histories', function ($table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function ($table) {
            $table->id();
            $table->foreignId('chat_history_id')->constrained('chat_histories')->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->longText('content');
            $table->timestamps();
        });
    }
};
