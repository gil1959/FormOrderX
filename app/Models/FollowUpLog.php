<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUpLog extends Model
{
    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'channel',
        'key',
        'phone',
        'message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
