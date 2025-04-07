<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mention extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'author_handle',
        'text',
        'post_url',
        'post_indexed_at',
        'sentiment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'post_indexed_at' => 'datetime',
        'sentiment' => 'float',
    ];

    /**
     * Get the user that owns the mention.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
