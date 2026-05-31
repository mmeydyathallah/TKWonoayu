<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('guardian_telegram_chats') || Schema::hasColumn('guardian_telegram_chats', 'selected_student_id')) {
            return;
        }

        Schema::table('guardian_telegram_chats', function (Blueprint $table): void {
            $table->foreignId('selected_student_id')
                ->nullable()
                ->after('telegram_username')
                ->constrained('students')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('guardian_telegram_chats') || ! Schema::hasColumn('guardian_telegram_chats', 'selected_student_id')) {
            return;
        }

        Schema::table('guardian_telegram_chats', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('selected_student_id');
        });
    }
};
