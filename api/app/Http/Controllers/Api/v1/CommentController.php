<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/posts/{postId}/comments",
     *     tags={"Comments"},
     *     summary="Créer un commentaire pour un post",
     *     @OA\Parameter(name="postId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"contenu","user_id"},
     *             @OA\Property(property="contenu", type="string"),
     *             @OA\Property(property="user_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Commentaire créé"),
     *     @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function createComment(Request $request, int $postId)
    {
//        $user = Auth::user();
//        if(!$user){
//            return response(status: 401,content: "Access denied");
//        }
        $validated = $request->validate([
            'contenu' => 'required|string',
            'user_id' => 'required|exists:users,id',

        ]);


        $comment = new Commentaire();
        $comment->contenu = $validated['contenu'];
        $comment->post_id = $postId;
        $comment->user_id = $validated['user_id'];
        $comment->status = 'active';
        //dd($comment);
        $comment->save();

        return response()->json([
            "message" => "Commentaire ajouté avec succès",
            "comment" => $comment
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/comments/{id}",
     *     tags={"Comments"},
     *     summary="Supprimer un commentaire",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Commentaire supprimé"),
     *     @OA\Response(response=404, description="Non trouvé")
     * )
     */
    public function deleteComment(int $id)
    {
        $comment = Commentaire::findOrFail($id);
        $comment->delete();

        return response()->json([
            "message" => "Commentaire supprimé avec succès"
        ]);
    }
}
