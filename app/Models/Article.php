<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;

    protected $fillable = [
        'publisher',
        'author',
        'title',
        'description',
        'url',
        'urlToImage',
        'publishedAt',
        'content'
    ];

    protected $casts = [
        'publishedAt' => 'datetime',
    ];
}
