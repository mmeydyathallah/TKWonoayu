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
            if ($this->indexExists('students_rfid_code_unique')) {
                $table->dropUnique('students_rfid_code_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->unique('rfid_code');
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
