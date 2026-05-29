<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price',
        'discount_percent',
        'max_group_members',
        'max_groups',
        'features'
    ];

    // Helper to get features as an array
    public function getFeaturesListAttribute(): array
    {
        return empty($this->features) ? [] : explode(',', $this->features);
    }
}
