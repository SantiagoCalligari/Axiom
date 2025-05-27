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
        // Solo modificar la tabla existente
        Schema::table('subject_user', function (Blueprint $table) {
            if (!Schema::hasColumn('subject_user', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_user', function (Blueprint $table) {
            if (Schema::hasColumn('subject_user', 'created_at')) {
                $table->dropColumn(['created_at', 'updated_at']);
            }
        });
    }
};
