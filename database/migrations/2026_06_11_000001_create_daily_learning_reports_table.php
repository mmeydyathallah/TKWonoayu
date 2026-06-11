<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_learning_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('assessed_on')->index();
            $table->string('class_group')->nullable();
            $table->string('agama_budi_pekerti_score', 8)->nullable();
            $table->text('agama_budi_pekerti_narrative')->nullable();
            $table->string('jati_diri_score', 8)->nullable();
            $table->text('jati_diri_narrative')->nullable();
            $table->string('literasi_steam_score', 8)->nullable();
            $table->text('literasi_steam_narrative')->nullable();
            $table->text('kokurikuler_description')->nullable();
            $table->string('extracurricular_implementation')->nullable();
            $table->string('extracurricular_activity')->nullable();
            $table->string('extracurricular_score_label', 8)->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'assessed_on']);
        });

        Schema::create('daily_learning_report_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_learning_report_id')
                ->constrained('daily_learning_reports')
                ->cascadeOnDelete();
            $table->string('domain_code', 64);
            $table->unsignedTinyInteger('slot');
            $table->string('title')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->unique(['daily_learning_report_id', 'domain_code', 'slot'], 'daily_learning_photo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_learning_report_photos');
        Schema::dropIfExists('daily_learning_reports');
    }
};
