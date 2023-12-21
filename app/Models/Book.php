<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'book_id';

    protected $fillable = [
        'title',
        'isbn',
        'publisher_id',
        'publish_date',
        'image_url',
        'description',
        'num_pages',
        'language',
        'url_key'
    ];

    /**
     * The genres that belong to the book.
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(
            Genre::class,
            'books_genres',
            'book_id',
            'genre_id'
        );
    }

    /**
     * The authors of the book.
     */
    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(
            Author::class,
            'books_authors',
            'book_id',
            'author_id'
        );
    }
}
