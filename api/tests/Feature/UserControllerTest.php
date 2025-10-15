<?php

namespace Tests\Feature;

use App\Mail\ActivationEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d’un utilisateur
     */
    public function test_create_user_success()
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
            'dateOfBirth' => '2000-05-10',
            'telephone' => '+22991234567'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }

    /**
     * Test si la validation échoue (âge invalide)
     */
    public function test_create_user_with_invalid_age()
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
            'dateOfBirth' => '2015-01-01', // trop jeune
            'telephone' => '+22998765432'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => "L'âge doit être compris entre 13 et 35 ans."
            ]);
    }

    /**
     * Test récupération de la liste des utilisateurs
     */
    public function test_get_users_list()
    {
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'email']
                ]
            ]);
    }

    /**
     * Test de la récupération d’un utilisateur spécifique
     */
    public function test_get_specific_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email
                ]
            ]);
    }

    /**
     * Test de mise à jour d’un utilisateur
     */
    public function test_update_user()
    {
        $user = User::factory()->create();

        $response = $this->putJson("/api/v1/users/{$user->id}", [
            'name' => 'Nom mis à jour',
            'email' => 'updated@example.com'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès',
                'data' => [
                    'email' => 'updated@example.com'
                ]
            ]);
    }

    /**
     * Test suppression d’un utilisateur
     */
    public function test_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Test envoi d’un e-mail d’activation
     */
    public function test_resend_activation_email()
    {
        Mail::fake();

        $user = User::factory()->create([
            'is_active' => false,
        ]);

        // Crée un token si tu utilises Sanctum ou Passport
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/resend/email');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Un e-mail d’activation vous a été envoyé.']);

        Mail::assertSent(ActivationEmail::class);
    }


    /**
     * Test activation d’un compte
     */
    public function test_activate_account()
    {
        $user = User::factory()->create([
            'activation_token' => Str::random(60),
            'is_active' => false,
        ]);

        $response = $this->getJson("/api/activate-account/{$user->activation_token}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Compte activé avec succès !']);

        $this->assertTrue($user->fresh()->is_active);
        $this->assertNull($user->fresh()->activation_token);
    }
}
