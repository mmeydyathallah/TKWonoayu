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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_no')->unique();
            $table->string('nisn')->nullable()->unique();
            $table->string('class_group');
            $table->string('school_year');
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->string('nik')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 16)->nullable();
            $table->string('religion', 32)->nullable();
            $table->unsignedTinyInteger('sibling_order')->nullable();
            $table->unsignedTinyInteger('siblings_total')->nullable();
            $table->text('address')->nullable();
            $table->decimal('distance_to_school_km', 5, 2)->nullable();
            $table->text('avatar_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

