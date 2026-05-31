<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuardianTelegramChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number_normalized',
        'chat_id',
        'telegram_user_id',
        'telegram_username',
        'selected_student_id',
    ];

    public function selectedStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'selected_student_id');
    }
}
