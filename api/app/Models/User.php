<?php

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

#[ApiResource]
/**
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   required={"id", "name", "email"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Ronaldo Awademe"),
 *   @OA\Property(property="email", type="string", example="ronaldo@gmail.com"),
 *   @OA\Property(property="dateOfBirth", type="string", format="date", example="2000-05-10"),
 *   @OA\Property(property="work", type="string", example="Développeur Web"),
 *   @OA\Property(property="photo", type="string", example="https://example.com/photo.jpg"),
 *   @OA\Property(property="telephone", type="string", example="+22997000000"),
 *   @OA\Property(property="bio", type="string", example="Passionné de code et de café ☕"),
 *   @OA\Property(property="is_active", type="boolean", example=true),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-09T12:00:00Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-09T12:00:00Z")
 * )
 */

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'dateOfBirth',
        'is_active',
        'work',
        'photo',
        'role',
        'enable_notifiction_push',
        'enable_notifiction_email',
        'bio',
        'telephone',
        'activation_token'

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dateOfBirth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class, 'user_id', 'id');
    }

    /**
     * Demandes d'amitié envoyées par cet utilisateur
     */
    public function sentFriendRequests()
    {
        return $this->hasMany(Relation::class, 'user_id');
    }

    /**
     * Demandes d'amitié reçues par cet utilisateur
     */
    public function receivedFriendRequests()
    {
        return $this->hasMany(Relation::class, 'friend_id');
    }

    /**
     * Tous les amis acceptés (relation bidirectionnelle)
     */
    public function friends()
    {
        return $this->belongsToMany(User::class, 'relations', 'user_id', 'friend_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps()
            ->withPivot('status');
    }

    /**
     * Amis où l'utilisateur est le destinataire
     */
    public function friendsOfMine()
    {
        return $this->belongsToMany(User::class, 'relations', 'friend_id', 'user_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps()
            ->withPivot('status');
    }

    /**
     * Obtenir tous les amis (combinaison des deux directions)
     */
    public function getAllFriends()
    {
        return $this->friends->merge($this->friendsOfMine);
    }

    /**
     * Demandes d'amitié en attente reçues
     */
    public function pendingFriendRequests()
    {
        return $this->belongsToMany(User::class, 'relations', 'friend_id', 'user_id')
            ->wherePivot('status', 'pending')
            ->withTimestamps()
            ->withPivot('status');
    }

    /**
     * Vérifier si deux utilisateurs sont amis
     */
    public function isFriendWith(User $user): bool
    {
        return $this->friends()->where('friend_id', $user->id)->exists() ||
            $this->friendsOfMine()->where('user_id', $user->id)->exists();
    }

    /**
     * Vérifier s'il y a une demande d'ami en attente
     */
    public function hasPendingFriendRequestFrom(User $user): bool
    {
        return Relation::where('user_id', $user->id)
            ->where('friend_id', $this->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Vérifier si cet utilisateur a envoyé une demande à un autre
     */
    public function hasSentFriendRequestTo(User $user): bool
    {
        return Relation::where('user_id', $this->id)
            ->where('friend_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Envoyer une demande d'ami
     */
    public function sendFriendRequestTo(User $user)
    {
        if ($this->id === $user->id) {
            throw new \Exception("Vous ne pouvez pas vous envoyer une demande d'ami à vous-même.");
        }

        if ($this->isFriendWith($user)) {
            throw new \Exception("Vous êtes déjà ami avec cet utilisateur.");
        }

        if ($this->hasSentFriendRequestTo($user)) {
            throw new \Exception("Vous avez déjà envoyé une demande d'ami à cet utilisateur.");
        }

        // Si l'autre utilisateur a déjà envoyé une demande, l'accepter automatiquement
        if ($this->hasPendingFriendRequestFrom($user)) {
            $friendship = Relation::where('user_id', $user->id)
                ->where('friend_id', $this->id)
                ->first();
            $friendship->status = 'accepted';
            $friendship->save();
            return $friendship;
        }

        return Relation::create([
            'user_id' => $this->id,
            'friend_id' => $user->id,
            'status' => 'pending'
        ]);
    }

    /**
     * Accepter une demande d'ami
     */
    public function acceptFriendRequestFrom(User $user)
    {
        $friendship = Relation::where('user_id', $user->id)
            ->where('friend_id', $this->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->status = 'accepted';
        $friendship->save();

        return $friendship;
    }

    /**
     * Rejeter une demande d'ami
     */
    public function rejectFriendRequestFrom(User $user)
    {
        $friendship = Relation::where('user_id', $user->id)
            ->where('friend_id', $this->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->status = 'rejected';
        $friendship->save();

        return $friendship;
    }

    /**
     * Supprimer une amitié
     */
    public function unfriend(User $user)
    {
        Relation::where(function ($query) use ($user) {
            $query->where('user_id', $this->id)
                ->where('friend_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('friend_id', $this->id);
        })->delete();
    }

    /**
     * Bloquer un utilisateur
     */
    public function blockUser(User $user)
    {
        $this->unfriend($user);

        return Relation::updateOrCreate(
            [
                'user_id' => $this->id,
                'friend_id' => $user->id
            ],
            [
                'status' => 'blocked'
            ]
        );
    }

    /**
     * Override the default password reset notification to use an API/front link
     * and avoid relying on a web route named 'password.reset'.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
