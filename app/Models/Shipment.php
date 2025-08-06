<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'order_number', 'shipment_reference', 'status', 'provider', 'label_url', 'raw_response'
    ];

    protected $casts = [
        'raw_response' => 'array',
    ];
}
