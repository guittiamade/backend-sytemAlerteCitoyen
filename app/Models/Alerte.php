<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alerte extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'description', 'photo', 'localisation', 'statut',
        'citoyen_id', 'gestionnaire_id', 'direction_id', 'type_alerte_id',
    ];

    public function citoyen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'citoyen_id');
    }

    public function gestionnaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gestionnaire_id');
    }

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeAlerte::class, 'type_alerte_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(AlerteHistory::class);
    }
}


