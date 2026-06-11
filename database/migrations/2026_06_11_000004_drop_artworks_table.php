<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('artworks');
    }

    public function down(): void
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('aspects')->nullable();
            $table->string('score_label', 8)->nullable();
            $table->unsignedTinyInteger('score_value')->nullable();
            $table->string('status', 24)->default('pending');
            $table->text('image_url')->nullable();
            $table->date('created_on')->nullable();
            $table->timestamps();
        });
    }
};
