<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolAnnouncement extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'published_on',
        'category',
    ];

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
        ];
    }
}

