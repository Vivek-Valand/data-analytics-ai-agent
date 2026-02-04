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
        Schema::create('chat_histories', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_history_id')->constrained('chat_histories')->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->longText('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_histories');
    }
};
