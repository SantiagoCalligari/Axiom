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
        //
        Schema::create('professional_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('profile_picture');
            $table->enum('prefix', ['Dr.', 'Dra.']);
            $table->enum('document_type', ['CUIT', 'CUIL', 'DNI']);
            $table->string('document_number');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('signature');
            $table->string('logo');
            $table->string('address');
            $table->string('personalized_stamp')->nullable();
            $table->string('profession');
            $table->string('specialty');
            $table->string('registration_type'); //Matricula
            $table->string('registration_number');
            $table->string('jurisdiction');
            $table->string('document_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('professional_profile');
        //
    }
};
