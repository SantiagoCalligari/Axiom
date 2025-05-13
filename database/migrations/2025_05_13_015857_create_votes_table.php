<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('votable');
            $table->enum('vote_type', ['up', 'down']);
            $table->timestamps();

            // Un usuario solo puede votar una vez por cada votable
            $table->unique(['user_id', 'votable_type', 'votable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
}; 