<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('student_id');
            $table->unsignedBigInteger('reply_to')->nullable()->after('parent_id');
            $table->foreign('parent_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('reply_to')->references('id')->on('feedbacks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['reply_to']);
            $table->dropColumn(['parent_id', 'reply_to']);
        });
    }
};
