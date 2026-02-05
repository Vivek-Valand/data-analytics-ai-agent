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
        Schema::create('database_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('connection')->default('mysql');
            $table->string('host')->default('127.0.0.1');
            $table->string('port')->default('3306');
            $table->string('database');
            $table->string('username');
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_configurations');
    }
};
