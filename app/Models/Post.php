<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'type',
        'authors',
        'journal_name',
        'conference_name',
        'hakki_type',
        'publisher',
        'year',
        'volume',
        'issue',
        'pages',
        'doi',
        'isbn',
        'status',
        'description',
        'link',
        'tags',
    ];

    protected $casts = [
        'authors' => 'array',
        'tags' => 'array',
        'year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

