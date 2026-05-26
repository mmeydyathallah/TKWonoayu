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
        Schema::create('parent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('guardian_name');
            $table->string('guardian_email')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_nik')->nullable();
            $table->string('father_job')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_nik')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('mother_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_profiles');
    }
};

