<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedSession extends Model
{
    protected $fillable = [
        'form_id',
        'user_id',
        'session_key',
        'data',
        'converted',
        'last_activity_at',
        'followup_sent_at','last_followup_key','last_followup_at',

    ];

    protected $casts = [
    'data' => 'array',
    'converted' => 'boolean',
    'last_activity_at' => 'datetime',

    'followup_sent_at' => 'datetime',
    'last_followup_at' => 'datetime',
];


    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
