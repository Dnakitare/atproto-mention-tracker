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
        'post_id',
        'author_did',
        'author_handle',
        'post_text',
        'post_data',
        'post_indexed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'post_data' => 'array',
        'post_indexed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the mention.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
