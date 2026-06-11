<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_learning_report_extracurriculars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_learning_report_id');
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->string('implementation')->nullable();
            $table->string('activity')->nullable();
            $table->string('score_label', 8)->nullable();
            $table->timestamps();

            $table->index(['daily_learning_report_id', 'sort_order'], 'daily_learning_extra_report_order_idx');
            $table->foreign('daily_learning_report_id', 'daily_learning_extra_report_fk')
                ->references('id')
                ->on('daily_learning_reports')
                ->cascadeOnDelete();
        });

        DB::table('daily_learning_reports')
            ->where(function ($query) {
                $query->whereNotNull('extracurricular_implementation')
                    ->orWhereNotNull('extracurricular_activity')
                    ->orWhereNotNull('extracurricular_score_label');
            })
            ->chunkById(100, function ($reports) {
                $now = now();
                $rows = [];

                foreach ($reports as $report) {
                    $rows[] = [
                        'daily_learning_report_id' => $report->id,
                        'sort_order' => 1,
                        'implementation' => $report->extracurricular_implementation,
                        'activity' => $report->extracurricular_activity,
                        'score_label' => $report->extracurricular_score_label,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($rows !== []) {
                    DB::table('daily_learning_report_extracurriculars')->insert($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_learning_report_extracurriculars');
    }
};
