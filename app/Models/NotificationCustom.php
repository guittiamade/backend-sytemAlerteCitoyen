<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationCustom extends Model
{
    use HasFactory;

    protected $table = 'notifications_custom';

    protected $fillable = [
        'message', 'date_envoi', 'statut', 'utilisateur_id', 'alerte_id',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function alerte(): BelongsTo
    {
        return $this->belongsTo(Alerte::class, 'alerte_id');
    }
}


