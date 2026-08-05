<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $fillable = [
        'type',
        'name',
        'federal_district',
    ];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
