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
        Schema::create('webhook_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_card_id'); // Column for webhook cards ID
            $table->string('webhook_card_name');
            $table->enum('status', ['pending', 'completed'])->default('pending'); // Column for status, default is 'pending'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_tasks');
    }
};
