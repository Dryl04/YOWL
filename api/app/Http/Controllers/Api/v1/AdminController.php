<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Commentaire;
use App\Models\Post;
use App\Models\Relation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/admin/users",
     *     tags={"Admin"},
     *     summary="Récupère la liste de tous les utilisateurs (admin)",
     *     @OA\Response(response=200, description="Liste des utilisateurs"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function getAdminUsers()
    {
        $user = Auth::user();
        if($user->role !== 'admin'){
            return response(content: "Access denied" ,status: 401);
        }
        $users = User::all();
        return response()->json($users);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/posts",
     *     tags={"Admin"},
     *     summary="Récupère la liste de tous les posts (admin)",
     *     @OA\Response(response=200, description="Liste des posts"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function getAdminPosts()
    {
        $user = Auth::user();
        if($user->role !== 'admin'){
            return response(content: "Access denied" ,status: 401);
        }
        $posts = Post::all();
        return response()->json($posts);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/admin/stats",
     *     tags={"Admin"},
     *     summary="Récupère des statistiques globales (admin)",
     *     @OA\Response(response=200, description="Statistiques retournées"),
     *     @OA\Response(response=401, description="Non autorisé")
     * )
     */
    public function getAdminStats()
    {
        $user = Auth::user();
        if($user->role !== 'admin'){
            return response(content: "Access denied" ,status: 401);
        }

        $users = User::all();
        $posts = Post::all();
        $comments = Commentaire::all();
        $relation = Relation::all();

        return response()->json(compact('users','posts','comments','relation'));
    }

}
