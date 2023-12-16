<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'genre_id';

    protected $fillable = [
        'name',
        'url_key'
    ];

    protected $hidden = ['pivot'];

    /**
     * The authors that belong to the genre.
     */
    public function author(): BelongsToMany
    {
        return $this->belongsToMany(
            Author::class,
            'authors_genres',
            'genre_id',
            'author_id'
        );
    }
}
