<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'mobile',
        'city',
        'address',
        'lat',
        'lng'
    ];

    /**
     * Get the users that belong to this warehouse.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
