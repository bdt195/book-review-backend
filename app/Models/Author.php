<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'author_id';

    protected $fillable = [
        'name',
        'url_key'
    ];

    protected $hidden = ['pivot'];

    /**
     * The genres that belong to the author.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(
            Genre::class,
            'authors_genres',
            'author_id',
            'genre_id'
        );
    }

    /**
     * The books that belong to the author.
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(
            Book::class,
            'books_authors',
            'author_id',
            'book_id'
        );
    }
}
