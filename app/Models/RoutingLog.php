<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutingLog extends Model
{
    protected $fillable = [
        'order_number',
        'chosen_provider',
        'context',
        'reason'
    ];

    protected $casts = [
        'context' => 'array',
    ];
}
