<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Artwork extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'title',
        'description',
        'aspects',
        'score_label',
        'score_value',
        'status',
        'image_url',
        'created_on',
    ];

    protected function casts(): array
    {
        return [
            'created_on' => 'date',
            'score_value' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

