<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('type', 30)->default('Anime');
            $table->string('genre');
            $table->decimal('score', 3, 1)->default(0);
            $table->unsignedSmallInteger('episodes')->default(1);
            $table->string('aura');
            $table->text('synopsis');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
