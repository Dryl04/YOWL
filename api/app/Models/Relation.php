<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ApiResource]
class Relation extends Model
{
    //use HasFactory;

    protected $fillable = [
        'user_id',
        'friend_id',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * L'utilisateur qui a envoyé la demande
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * L'utilisateur qui a reçu la demande
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    /**
     * Scope pour les amitiés acceptées
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Scope pour les demandes en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les amitiés bloquées
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }
}
