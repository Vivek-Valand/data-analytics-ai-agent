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
        Schema::create('analytics_chat_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('analytics_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_history_id');
            $table->string('role'); // 'user' or 'assistant'
            $table->text('content');
            $table->timestamps();

            $table->foreign('chat_history_id')
                ->references('id')
                ->on('analytics_chat_histories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_chat_messages');
        Schema::dropIfExists('analytics_chat_histories');
    }
};
