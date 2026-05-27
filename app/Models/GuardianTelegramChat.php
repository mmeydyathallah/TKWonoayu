<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuardianTelegramChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number_normalized',
        'chat_id',
        'telegram_user_id',
        'telegram_username',
    ];
}
