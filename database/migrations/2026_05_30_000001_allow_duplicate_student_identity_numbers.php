<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            if ($this->indexExists('students_student_no_unique')) {
                $table->dropUnique('students_student_no_unique');
            }

            if ($this->indexExists('students_nisn_unique')) {
                $table->dropUnique('students_nisn_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->unique('student_no');
            $table->unique('nisn');
        });
    }

    private function indexExists(string $indexName): bool
    {
        return ! empty(DB::select(
            'SHOW INDEX FROM students WHERE Key_name = ?',
            [$indexName]
        ));
    }
};
