<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RelationController extends Controller
{
    /**
     * Envoyer une demande d'ami
     */
    public function sendFriendRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'friend_id' => 'required|integer|exists:users,id'
            ]);

            $user = Auth::user();
            $friend = User::findOrFail($validated['friend_id']);

            // Vérifier si c'est le même utilisateur
            if ($user->id === $friend->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas vous envoyer une demande d\'ami à vous-même'
                ], 422);
            }

            $friendship = $user->sendFriendRequestTo($friend);

            // Charger les relations pour la réponse
            $friendship->load(['sender', 'receiver']);

            return response()->json([
                'success' => true,
                'message' => 'Demande d\'ami envoyée avec succès',
                'data' => $friendship
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Accepter une demande d'ami
     */
    public function acceptFriendRequest($friendshipId)
    {
        try {
            $user = Auth::user();
            $friendship = Relation::with(['sender', 'receiver'])->findOrFail($friendshipId);

            // Vérifier que c'est bien le destinataire
            if ($friendship->friend_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à accepter cette demande'
                ], 403);
            }

            // Vérifier que la demande est en attente
            if ($friendship->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande n\'est plus en attente'
                ], 422);
            }

            $sender = User::findOrFail($friendship->user_id);
            $user->acceptFriendRequestFrom($sender);

            return response()->json([
                'success' => true,
                'message' => 'Demande d\'ami acceptée',
                'data' => $friendship->fresh(['sender', 'receiver'])
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Demande d\'ami non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'acceptation de la demande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeter une demande d'ami
     */
    public function rejectFriendRequest($friendshipId)
    {
        try {
            $user = Auth::user();
            $friendship = Relation::with(['sender', 'receiver'])->findOrFail($friendshipId);

            // Vérifier que c'est bien le destinataire
            if ($friendship->friend_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à rejeter cette demande'
                ], 403);
            }

            // Vérifier que la demande est en attente
            if ($friendship->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande n\'est plus en attente'
                ], 422);
            }

            $sender = User::findOrFail($friendship->user_id);
            $user->rejectFriendRequestFrom($sender);

            return response()->json([
                'success' => true,
                'message' => 'Demande d\'ami rejetée'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Demande d\'ami non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rejet de la demande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir la liste de tous les amis
     */
    public function getFriends()
    {
        try {
            $user = Auth::user();
            $friends = $user->getAllFriends();

            return response()->json([
                'success' => true,
                'count' => $friends->count(),
                'data' => $friends
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des amis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les demandes d'ami en attente (reçues)
     */
    public function getPendingRequests()
    {
        try {
            $user = Auth::user();
            $pendingRequests = $user->pendingFriendRequests;

            return response()->json([
                'success' => true,
                'count' => $pendingRequests->count(),
                'data' => $pendingRequests
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes en attente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les demandes d'ami envoyées
     */
    public function getSentRequests()
    {
        try {
            $user = Auth::user();
            $sentRequests = Relation::with('receiver')
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->get();

            return response()->json([
                'success' => true,
                'count' => $sentRequests->count(),
                'data' => $sentRequests
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes envoyées',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un ami
     */
    public function unfriend($friendId)
    {
        try {
            $user = Auth::user();
            $friend = User::findOrFail($friendId);

            // Vérifier si ils sont amis
            if (!$user->isFriendWith($friend)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas ami avec cet utilisateur'
                ], 422);
            }

            $user->unfriend($friend);

            return response()->json([
                'success' => true,
                'message' => 'Ami supprimé avec succès'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'ami',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Annuler une demande d'ami envoyée
     */
    public function cancelFriendRequest($friendId)
    {
        try {
            $user = Auth::user();

            $friendship = Relation::where('user_id', $user->id)
                ->where('friend_id', $friendId)
                ->where('status', 'pending')
                ->firstOrFail();

            $friendship->delete();

            return response()->json([
                'success' => true,
                'message' => 'Demande d\'ami annulée avec succès'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Demande d\'ami non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation de la demande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bloquer un utilisateur
     */
    public function blockUser($userId)
    {
        try {
            $user = Auth::user();
            $userToBlock = User::findOrFail($userId);

            // Vérifier si c'est le même utilisateur
            if ($user->id === $userToBlock->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas vous bloquer vous-même'
                ], 422);
            }

            $user->blockUser($userToBlock);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur bloqué avec succès'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du blocage de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Débloquer un utilisateur
     */
    public function unblockUser($userId)
    {
        try {
            $user = Auth::user();

            $blocked = Relation::where('user_id', $user->id)
                ->where('friend_id', $userId)
                ->where('status', 'blocked')
                ->first();

            if (!$blocked) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet utilisateur n\'est pas bloqué'
                ], 422);
            }

            $blocked->delete();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur débloqué avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du déblocage de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir la liste des utilisateurs bloqués
     */
    public function getBlockedUsers()
    {
        try {
            $user = Auth::user();

            $blockedUsers = Relation::with('receiver')
                ->where('user_id', $user->id)
                ->where('status', 'blocked')
                ->get()
                ->pluck('receiver');

            return response()->json([
                'success' => true,
                'count' => $blockedUsers->count(),
                'data' => $blockedUsers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs bloqués',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier le statut de relation avec un utilisateur
     */
    public function checkFriendshipStatus($userId)
    {
        try {
            $user = Auth::user();
            $otherUser = User::findOrFail($userId);

            if ($user->id === $otherUser->id) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => 'self',
                        'message' => 'C\'est vous'
                    ]
                ]);
            }

            // Vérifier si amis
            if ($user->isFriendWith($otherUser)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => 'friends',
                        'message' => 'Vous êtes amis'
                    ]
                ]);
            }

            // Vérifier si demande envoyée
            if ($user->hasSentFriendRequestTo($otherUser)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => 'request_sent',
                        'message' => 'Demande envoyée'
                    ]
                ]);
            }

            // Vérifier si demande reçue
            if ($user->hasPendingFriendRequestFrom($otherUser)) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => 'request_received',
                        'message' => 'Demande reçue'
                    ]
                ]);
            }

            // Vérifier si bloqué
            $blocked = Relation::where('user_id', $user->id)
                ->where('friend_id', $userId)
                ->where('status', 'blocked')
                ->exists();

            if ($blocked) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => 'blocked',
                        'message' => 'Utilisateur bloqué'
                    ]
                ]);
            }

            // Pas de relation
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'none',
                    'message' => 'Aucune relation'
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du statut',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
