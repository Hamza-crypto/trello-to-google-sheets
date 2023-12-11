<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('board_id');
            $table->string('card_id');
            $table->string('card_name');
            $table->enum('status', ['pending', 'completed'])->default('pending'); // Column for status, default is 'pending'
            $table->timestamps();
        });
    }

    /
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
    }
};