<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publisher extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'publisher_id';

    protected $fillable = [
        'name',
        'url_key'
    ];

    /**
     * The books that belong to the author.
     */
    public function books(): HasMany
    {
        return $this->hasMany(
            Book::class,
            'publisher_id',
            'publisher_id'
        );
    }
}
