<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Direction extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'direction_generale'];

    public function alertes(): HasMany
    {
        return $this->hasMany(Alerte::class);
    }
}


