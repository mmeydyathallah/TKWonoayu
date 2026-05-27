<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('guardian_telegram_chats')) {
            return;
        }

        Schema::create('guardian_telegram_chats', function (Blueprint $table): void {
            $table->id();
            $table->string('phone_number_normalized', 32)->unique();
            $table->string('chat_id', 64);
            $table->string('telegram_user_id', 64)->nullable();
            $table->string('telegram_username', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardian_telegram_chats');
    }
};
