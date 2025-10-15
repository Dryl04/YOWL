<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ApiResource]
class Post extends Model
{
    use HasFactory;
    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function commentaires()
    {
        return $this->hasMany(Commentaire::class, 'post_id', 'id');
    }

    protected $fillable = [
        'description',
        'image',
        'link',
        'likes',
        'likes_users',
        'shares_users',
        'shares',
        'user_id',
        'status'
    ];

    protected $casts = [
        'likes_users' => 'array',
        'shares_users' => 'array'
    ];
}
