<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'embed_token',
        'description',
        'base_price',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price' => 'decimal:2',
        'settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function abandonedSessions(): HasMany
    {
        return $this->hasMany(AbandonedSession::class);
    }
}
