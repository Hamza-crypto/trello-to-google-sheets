<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_id');
            $table->string('name');
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('message')->default("");
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};