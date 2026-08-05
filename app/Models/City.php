<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    protected $fillable = [
        'post_index',
        'type',
        'name',
        'region_id',
        'district_id',
        'settlement_type',
        'settlement',
        'kladr_code',
        'fias_code',
        'fias_level',
        'center_sign',
        'okato_code',
        'oktmo_code',
        'tax_code',
        'timezone',
        'latitude',
        'longitude',
        'federal_district',
        'population',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'center_sign' => 'integer',
        'population' => 'integer',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
