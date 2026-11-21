<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pivot table koleksi media milik pengguna (many-to-many).
     * Nama mengikuti konvensi Laravel: singular, urut alfabet -> media_user.
     */
    public function up(): void
    {
        Schema::create('media_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Mencegah media yang sama masuk dua kali ke koleksi user yang sama,
            // termasuk saat dua request datang bersamaan (race condition).
            $table->unique(['media_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_user');
    }
};
