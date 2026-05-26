<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolActivity extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'color',
        'icon',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
        ];
    }
}

