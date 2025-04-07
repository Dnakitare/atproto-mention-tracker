<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alert extends Model
{
    use HasFactory;

    /**
     * The relationships that should be eager loaded.
     */
    protected $with = ['user'];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'type',
        'conditions',
        'notification_channels',
        'last_triggered_at',
        'is_active',
        'notification_frequency',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'conditions' => 'array',
        'notification_channels' => 'array',
        'last_triggered_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the alert.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 