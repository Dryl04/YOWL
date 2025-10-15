<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Mail\ActivationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      summary="Récupérer la liste des utilisateurs",
     *      description="Retourne la liste complète des utilisateurs avec leurs posts et commentaires.",
     *      tags={"Utilisateurs"},
     *      @OA\Response(
     *          response=200,
     *          description="Liste récupérée avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User"))
     *          )
     *      )
     * )
     */
    public function getUsers()
    {
        $users = User::with(['posts', 'commentaires'])->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/users/{id}",
     *      summary="Récupérer un utilisateur spécifique",
     *      description="Retourne les informations détaillées d’un utilisateur donné.",
     *      tags={"Utilisateurs"},
     *      @OA\Parameter(
     *          name="id",
     *          description="ID de l'utilisateur",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Utilisateur trouvé",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function getUser(int $id)
    {
        $user = User::with(['posts', 'commentaires'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/users",
     *      summary="Créer un nouvel utilisateur",
     *      description="Crée un nouvel utilisateur après validation des données et vérification de l'âge (13-35 ans).",
     *      tags={"Utilisateurs"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password","password_confirmation","dateOfBirth","telephone"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="password", type="string", example="motdepasse123"),
     *              @OA\Property(property="password_confirmation", type="string", example="motdepasse123"),
     *              @OA\Property(property="dateOfBirth", type="string", format="date", example="2000-01-15"),
     *              @OA\Property(property="work", type="string", example="Développeur web"),
     *              @OA\Property(property="photo", type="string", example="https://example.com/photo.jpg"),
     *              @OA\Property(property="telephone", type="string", example="+22991234567"),
     *              @OA\Property(property="bio", type="string", example="Je suis un jeune entrepreneur béninois.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Utilisateur créé avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Utilisateur créé avec succès"),
     *              @OA\Property(property="data", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function createUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'email'       => 'required|email|unique:users,email',
                'password'    => 'required|string|min:8|confirmed',
                'dateOfBirth' => 'required|date|before:today',
                'work'        => 'nullable|string|max:255',
                'photo'       => 'nullable|url|max:500',
                'telephone'   => 'required|string|max:16',
                'bio'         => 'nullable|string|max:255',
            ]);

            $age = Carbon::parse($validated['dateOfBirth'])->age;
            if ($age < 13 || $age > 35) {
                return response()->json([
                    'success' => false,
                    'message' => "L'âge doit être compris entre 13 et 35 ans."
                ], 422);
            }

            $user = User::create([
                'name'        => $validated['name'],
                'email'       => $validated['email'],
                'password'    => Hash::make($validated['password']),
                'dateOfBirth' => $validated['dateOfBirth'],
                'work'        => $validated['work'] ?? null,
                'photo'       => $validated['photo'] ?? null,
                'is_active'   => true,
                'telephone'   => $validated['telephone'],
                'bio'         => $validated['bio'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'data' => $user
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Put(
     *      path="/api/v1/users/{id}",
     *      summary="Mettre à jour un utilisateur",
     *      description="Met à jour les informations d’un utilisateur existant.",
     *      tags={"Utilisateurs"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID de l'utilisateur à mettre à jour",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\RequestBody(
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="password", type="string", example="nouveaumdp123"),
     *              @OA\Property(property="password_confirmation", type="string", example="nouveaumdp123"),
     *              @OA\Property(property="dateOfBirth", type="string", format="date", example="2001-05-10"),
     *              @OA\Property(property="isActive", type="boolean", example=true),
     *              @OA\Property(property="work", type="string", example="Designer"),
     *              @OA\Property(property="photo", type="string", example="https://example.com/photo.jpg"),
     *              @OA\Property(property="telephone", type="string", example="+22998765432"),
     *              @OA\Property(property="bio", type="string", example="Description mise à jour")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Utilisateur mis à jour avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Utilisateur mis à jour avec succès"),
     *              @OA\Property(property="data", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Erreur de validation")
     * )
     */
    public function updateUser(Request $request, int $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name'        => 'sometimes|string|max:255',
                'email'       => 'sometimes|email|unique:users,email,' . $user->id,
                'password'    => 'sometimes|string|min:8|confirmed',
                'dateOfBirth' => 'sometimes|date|before:today',
                'isActive'    => 'sometimes|boolean',
                'work'        => 'sometimes|nullable|string|max:255',
                'photo'       => 'sometimes|nullable|url|max:500',
                'role'        => 'sometimes|string|max:6',
                'telephone'   => 'sometimes|string|max:16',
                'bio'         => 'sometimes|string|max:255',
            ]);

            if (isset($validated['dateOfBirth'])) {
                $age = Carbon::parse($validated['dateOfBirth'])->age;
                if ($age < 13 || $age > 35) {
                    return response()->json([
                        'success' => false,
                        'message' => "L'âge doit être compris entre 13 et 35 ans."
                    ], 422);
                }
            }

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès',
                'data' => $user->fresh()
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/users/{id}",
     *      summary="Supprimer un utilisateur",
     *      description="Supprime un utilisateur existant.",
     *      tags={"Utilisateurs"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID de l'utilisateur à supprimer",
     *          @OA\Schema(type="integer", example=1)
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Utilisateur supprimé avec succès",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Utilisateur supprimé avec succès")
     *          )
     *      ),
     *      @OA\Response(response=404, description="Utilisateur non trouvé")
     * )
     */
    public function deleteUser(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/resend/email",
     *     summary="Renvoyer l'e-mail d'activation du compte",
     *     description="Permet à un utilisateur non activé de recevoir un nouvel e-mail d'activation. L'utilisateur doit être authentifié.",
     *     tags={"Authentification"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="E-mail d’activation envoyé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Un e-mail d’activation vous a été envoyé.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Le compte est déjà activé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Votre compte est déjà activé.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Utilisateur non authentifié ou token invalide"
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur lors de l'envoi de l'e-mail"
     *     )
     * )
     */


    public function resendEmailActivation(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié.'
            ], 401);
        }

        if ($user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte est déjà activé.'
            ], 400);
        }

        // Générer un nouveau token d’activation (facultatif)
        $token = Str::random(60);
        $user->activation_token = $token;
        $user->save();

        // Envoyer l’email d’activation
        Mail::to($user->email)->send(new ActivationEmail($user));

        return response()->json([
            'success' => true,
            'message' => 'Un e-mail d’activation vous a été envoyé.'
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/activate-account/{token}",
     *     summary="Activer un compte utilisateur via le lien d'activation",
     *     description="Active le compte de l'utilisateur correspondant au token reçu par e-mail.",
     *     tags={"Authentification"},
     *
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="Token d'activation envoyé à l'utilisateur par e-mail",
     *         @OA\Schema(type="string", example="a1b2c3d4e5f6g7h8i9j0")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Compte activé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Compte activé avec succès !")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Token invalide ou inexistant",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Lien d’activation invalide.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur lors de l'activation"
     *     )
     * )
     */


    public function activateAccount($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Lien d’activation invalide.'], 404);
        }

        $user->is_active = true;
        $user->activation_token = null;
        $user->save();

        return response()->json(['message' => 'Compte activé avec succès !']);
    }


}
