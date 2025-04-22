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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who uploaded it
            $table->foreignId('subject_id')->constrained()->onDelete('cascade'); // To which subject it belongs

            // Metadata fields
            $table->string('title')->nullable(); // Optional title
            $table->string('professor_name')->nullable();
            $table->string('semester')->nullable(); // e.g., '1C 2023', '2do Cuatrimestre'
            $table->integer('year')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->string('exam_type')->nullable();
            $table->date('exam_date')->nullable(); // Date the exam was actually taken

            // File information
            $table->string('file_path'); // Path relative to storage disk
            $table->string('original_file_name'); // Name when uploaded
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            // OCR (will be populated later if you add OCR)
            $table->longText('ocr_text')->nullable(); // To store the extracted text

            $table->timestamps(); // created_at is the upload date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
