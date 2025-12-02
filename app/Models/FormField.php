<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    protected $fillable = [
        'form_id',
        'label',
        'name',
        'type',
        'required',
        'options',
        'order',
        'is_active',
    ];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array',
        'is_active' => 'boolean',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
