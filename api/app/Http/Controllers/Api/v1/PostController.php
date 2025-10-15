<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Annotations as OA;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/posts",
     *     tags={"Posts"},
     *     summary="Récupérer tous les posts",
     *     @OA\Response(response=200, description="Liste des posts"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function getPosts()
    {
        try {
            $posts = Post::with(['user', 'commentaires.user'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/posts/{id}",
     *     tags={"Posts"},
     *     summary="Récupérer un post spécifique",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Post retourné"),
     *     @OA\Response(response=404, description="Post non trouvé")
     * )
     */
    public function getPost(int $id)
    {
        try {
            $post = Post::with(['user', 'commentaires.user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $post
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/posts",
     *     tags={"Posts"},
     *     summary="Créer un nouveau post",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"description","user_id"},
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="image", type="string", format="url"),
     *             @OA\Property(property="link", type="string", format="url"),
     *             @OA\Property(property="user_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Post créé"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function createPost(Request $request)
    {
        try {
            $validated = $request->validate([
                'description' => 'required|string|max:5000',
                'image' => 'nullable|url|max:1000',
                'link' => 'nullable|url|max:1000',
                'user_id' => 'required|exists:users,id',
            ]);

            $post = Post::create([
                'description' => $validated['description'],
                'image' => $validated['image'] ?? null,
                'link' => $validated['link'] ?? null,
                'user_id' => $validated['user_id'],
                'likes' => 0,
                'shares' => 0,
                'status' => 'active'
            ]);

            // Charger les relations pour la réponse
            $post->load(['user', 'commentaires']);

            return response()->json([
                'success' => true,
                'message' => 'Post créé avec succès',
                'data' => $post
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/posts/{id}",
     *     tags={"Posts"},
     *     summary="Mettre à jour un post",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(@OA\Property(property="description", type="string"))),
     *     @OA\Response(response=200, description="Post mis à jour"),
     *     @OA\Response(response=404, description="Post non trouvé")
     * )
     */
    public function updatePost(Request $request, int $id)
    {
        try {
            $post = Post::findOrFail($id);

            $validated = $request->validate([
                'description' => 'sometimes|string|max:5000',
                'image' => 'sometimes|nullable|url|max:1000',
                'link' => 'sometimes|nullable|url|max:1000',
            ]);

            $post->update($validated);

            // Recharger avec les relations
            $post->load(['user', 'commentaires.user']);

            return response()->json([
                'success' => true,
                'message' => 'Post mis à jour avec succès',
                'data' => $post
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post non trouvé'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/posts/{id}",
     *     tags={"Posts"},
     *     summary="Supprimer un post",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Post supprimé"),
     *     @OA\Response(response=404, description="Post non trouvé")
     * )
     */
    public function deletePost(int $id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post supprimé avec succès'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/posts/{id}/like",
     *     tags={"Posts"},
     *     summary="Liker un post",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Post liké"),
     *     @OA\Response(response=404, description="Post non trouvé")
     * )
     */
    public function likePost(int $id)
    {
        try {
            $post = Post::findOrFail($id);
            $likes_users = $post->likes_users;
            if(!in_array(auth()->id(), $likes_users)) {
                $post->increment('likes');
                $likes_users[] = auth()->id();
                $post->setAttribute('likes_users', $likes_users);
                $post->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Post liké avec succès',
                    'data' => [
                        'post_id' => $post->id,
                        'likes' => $post->likes
                    ]
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => "Vous avez déjà liker ce post"
                ]);
            }

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du like',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/posts/{id}/unlike",
     *     tags={"Posts"},
     *     summary="Unliker un post",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Like retiré"),
     *     @OA\Response(response=404, description="Post non trouvé")
     * )
     */
    public function unlikePost(int $id)
    {
        try {
            $post = Post::findOrFail($id);
            $likes_users = $post->likes_users;

            if ($post->likes > 0 && in_array(auth()->id(), $likes_users)) {
                $post->decrement('likes');
                $cle = array_search(auth()->id(), $likes_users);
                unset($likes_users[$cle]);
                $post->likes_users = $likes_users;
                $post->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Like retiré avec succès',
                    'data' => [
                        'post_id' => $post->id,
                        'likes' => $post->likes
                    ]
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => "Vous n'avez pas encore liker ce post"
                ]);
            }



        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du unlike',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/posts/{id}/share",
     *     tags={"Posts"},
     *     summary="Partager un post",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Post partagé"),
     *     @OA\Response(response=404, description="Post non trouvé")
     * )
     */
    public function sharePost(int $id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->increment('shares');
            $post->shares_users[] = auth()->id();
            return response()->json([
                'success' => true,
                'message' => 'Post partagé avec succès',
                'data' => [
                    'post_id' => $post->id,
                    'shares' => $post->shares
                ]
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du partage',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{userId}/posts",
     *     tags={"Posts"},
     *     summary="Récupérer les posts d'un utilisateur",
     *     @OA\Parameter(name="userId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Liste des posts utilisateur")
     * )
     */
    public function getUserPosts(int $userId)
    {
        try {
            $posts = Post::with(['user', 'commentaires.user'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des posts de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
