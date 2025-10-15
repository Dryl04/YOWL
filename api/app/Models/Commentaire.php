<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ApiResource]
class Commentaire extends Model
{
    /** @use HasFactory<\Database\Factories\CommentaireFactory> */
    use HasFactory;

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function Post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    protected $fillable = [
        'contenu',
        'status',
        'user_id',
        'post_id',
    ];
}
