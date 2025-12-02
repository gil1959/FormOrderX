<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $fillable = [
        'form_id',
        'user_id',
        'status',
        'total_price',
        'data',
        'source_url',
        'client_ip',
        'user_agent',
        'is_spam',
        'submitted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_spam' => 'boolean',
        'total_price' => 'decimal:2',
        'submitted_at' => 'datetime',
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
