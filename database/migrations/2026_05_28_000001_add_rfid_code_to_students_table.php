<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('students', 'rfid_code')) {
            return;
        }

        Schema::table('students', function (Blueprint $table): void {
            $table->string('rfid_code', 64)->nullable()->unique()->after('student_no');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('students', 'rfid_code')) {
            return;
        }

        Schema::table('students', function (Blueprint $table): void {
            $table->dropUnique('students_rfid_code_unique');
            $table->dropColumn('rfid_code');
        });
    }
};
