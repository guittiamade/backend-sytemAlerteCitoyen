<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeAlerte extends Model
{
    use HasFactory;

    protected $table = 'types_alertes';

    protected $fillable = ['nom', 'description'];

    public function alertes(): HasMany
    {
        return $this->hasMany(Alerte::class, 'type_alerte_id');
    }
}


