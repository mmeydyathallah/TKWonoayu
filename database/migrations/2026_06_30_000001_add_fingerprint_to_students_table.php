<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('students', 'fingerprint_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->unsignedSmallInteger('fingerprint_id')->nullable()->after('rfid_code');
                $table->binary('fingerprint_data')->nullable()->after('fingerprint_id');
                $table->timestamp('fingerprint_enrolled_at')->nullable()->after('fingerprint_data');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'fingerprint_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn(['fingerprint_id', 'fingerprint_data', 'fingerprint_enrolled_at']);
            });
        }
    }
};
