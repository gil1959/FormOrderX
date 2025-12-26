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
        'payment_status',
        'total_price',
        'data',
        'source_url',
        'client_ip',
        'user_agent',
        'is_spam',
        'submitted_at',
        'welcome_sent_at','followup1_sent_at','followup2_sent_at','followup3_sent_at','followup4_sent_at',
'last_followup_key','last_followup_at',

    ];



    protected $casts = [
    'data' => 'array',
    'is_spam' => 'boolean',
    'total_price' => 'decimal:2',
    'submitted_at' => 'datetime',

    'welcome_sent_at' => 'datetime',
    'followup1_sent_at' => 'datetime',
    'followup2_sent_at' => 'datetime',
    'followup3_sent_at' => 'datetime',
    'followup4_sent_at' => 'datetime',
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
