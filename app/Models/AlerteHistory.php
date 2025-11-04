<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlerteHistory extends Model
{
    use HasFactory;

    protected $fillable = ['alerte_id','user_id','action','from_status','to_status','note'];

    public function alerte(): BelongsTo
    {
        return $this->belongsTo(Alerte::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


