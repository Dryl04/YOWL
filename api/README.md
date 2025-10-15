# 🐦 YOWL API Documentation

API RESTful pour un réseau social développée avec Laravel 11, permettant la gestion d'utilisateurs, de publications, de commentaires et de relations d'amitié.

## 📋 Table des matières

- [Technologies utilisées](#technologies-utilisées)
- [Installation](#installation)
- [Configuration](#configuration)
- [Authentification](#authentification)
- [Endpoints](#endpoints)
    - [Home](#home)
    - [Authentification](#endpoints-authentification)
    - [Utilisateurs](#utilisateurs)
    - [Publications](#publications)
    - [Commentaires](#commentaires)
    - [Relations d'amitié](#relations-damitié)
- [Modèles de données](#modèles-de-données)
- [Codes de réponse](#codes-de-réponse)
- [Exemples d'utilisation](#exemples-dutilisation)

---

## 🛠️ Technologies utilisées

- **Laravel 11**
- **Laravel Sanctum** (Authentification API)
- **MySQL/PostgreSQL** (Base de données)
- **PHP 8.2+**

---

## 📦 Installation

### Prérequis
- PHP 8.2 ou supérieur
- Composer
- MySQL ou PostgreSQL

### Étapes d'installation
```bash
# Cloner le repository
git clone https://github.com/EpitechCodingAcademyPromo2025/C-DEV-160-COT-1-1-yowl-ronaldo.awademe.git
cd C-DEV-160-COT-1-1-yowl-ronaldo.awademe.git

# Installer les dépendances
composer install

# Copier le fichier .env
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer votre base de données dans .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yowl_db
DB_USERNAME=root
DB_PASSWORD=

# Exécuter les migrations
php artisan migrate

# Installer Sanctum
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

# Démarrer le serveur
php artisan serve

```

L'API sera accessible sur http://localhost:8000/api/v1

⚙️ Configuration
Sanctum Configuration
Assurez-vous que votre modèle User utilise le trait HasApiTokens :

```
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    // ...
}
   ```
🔐 Authentification
L'API utilise Laravel Sanctum pour l'authentification par token Bearer.
Obtenir un token

S'inscrire via /api/v1/register
Se connecter via /api/v1/login
Utiliser le token reçu dans le header Authorization: Bearer {token}

Routes protégées
Les routes protégées nécessitent le header suivant :

Authorization: Bearer votre_token_ici

📡 Endpoints
Base URL

http://localhost:8000/api/v1

Home
Informations sur l'API

GET /
 ```
{
  "status": true,
  "message": "Welcome to YOWL API",
  "data": {
    "service": "YOWL API",
    "version": "1.0.0",
    "langage": "en",
    "support": "king-of-php@epitech.eu"
  }
}
  ```
Endpoints Authentification
1. Inscription
POST /register
  ```
Body (JSON) :
{
  "name": "Ronaldo AWADEME",
  "email": "ronaldo.awademe@epitech.eu",
  "password": "password123",
  "password_confirmation": "password123",
  "dateOfBirth": "2001-07-26",
  "work": "Développeur fullstack",
  "photo": "https://i.pravatar.cc/150?img=1"
}
```
Validations :
 ```
name : requis, max 255 caractères
email : requis, email valide, unique
password : requis, min 8 caractères, confirmation requise
dateOfBirth : requis, format YYYY-MM-DD, avant aujourd'hui
work : optionnel, max 255 caractères
photo : optionnel, URL valide
Âge : entre 13 et 35 ans
```
Réponse (201) :
```
{
  "success": true,
  "message": "Utilisateur créé avec succès",
  "data": {
    "user": {
      "id": 1,
      "name": "Ronaldo AWADEME",
      "email": "ronaldo.awademe@epitech.eu",
      "dateOfBirth": "2001-07-26",
      "work": "Développeur fullstack",
      "photo": "https://i.pravatar.cc/150?img=1",
      "is_active": false,
      "created_at": "2025-10-04T10:00:00.000000Z"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz..."
  }
}
```
2. Connexion
POST /login
 ```
Body (JSON) :
{
  "email": "ronaldo.awademe@epitech.eu",
  "password": "password123"
}
Réponse (200) :
{
  "success": true,
  "message": "Connexion réussie",
  "data": {
    "user": { /* objet user */ },
    "token": "2|abcdefghijklmnopqrstuvwxyz..."
  }
}
```

4. Utilisateur connecté 🔒
GET /me
Authorization: Bearer {token}

Réponse (200) :
```
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Ronaldo AWADEME",
    "email": "ronaldo.awademe@epitech.eu",
    /* ... */
  }
}
```
Utilisateurs
1. Liste des utilisateurs
GET /users
Réponse (200) :
```
json{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Ronaldo AWADEME",
      "email": "ronaldo.awademe@epitech.eu",
      "work": "Développeur fullstack",
      "photo": "https://i.pravatar.cc/150?img=1",
      "posts": [],
      "commentaires": []
    }
  ]
}
```
2. Créer un utilisateur
POST /users
Body : Identique à /register (sans confirmation de mot de passe requise pour cette route)

3. Détails d'un utilisateur 🔒
GET /users/{id}
Authorization: Bearer {token}
Réponse (200) :
```
json{
  "success": true,
  "data": {
    "id": 1,
    "name": "Jean Dupont",
    /* ... */
  }
}
```
4. Mettre à jour un utilisateur 🔒
```
PUT /users/{id}
Authorization: Bearer {token}
Body (JSON) :
json{
  "name": "Jean Martin",
  "work": "Senior Developer"
}
```
Champs modifiables :

name, email, password, dateOfBirth, isActive, work, photo


5. Supprimer un utilisateur 🔒
DELETE /users/{id}
Authorization: Bearer {token}
Réponse (200) :
```
json{
  "success": true,
  "message": "Utilisateur supprimé avec succès"
}
```
Publications
1. Liste des publications
GET /posts
Réponse (200) :
```
json{
  "success": true,
  "data": [
    {
      "id": 1,
      "description": "Mon premier post !",
      "image": "https://images.unsplash.com/photo-...",
      "link": "https://example.com",
      "likes": 12,
      "shares": 5,
      "user_id": 1,
      "created_at": "2025-10-04T10:00:00.000000Z",
      "user": { /* objet user */ },
      "commentaires": []
    }
  ]
}
```
2. Détails d'une publication
GET /post/{id}
Réponse (200) :
```
json{
  "success": true,
  "data": {
    "id": 1,
    "description": "Mon premier post !",
    /* ... */
  }
}
```
3. Créer une publication 🔒
POST /posts
Authorization: Bearer {token}
Body (JSON) :
```
json{
  "description": "Nouveau post incroyable !",
  "image": "https://images.unsplash.com/photo-...",
  "link": "https://example.com",
  "user_id": 1
}
```
Validations :

description : requis, max 5000 caractères
image : optionnel, URL valide
link : optionnel, URL valide
user_id : requis, doit exister

Réponse (201) :
```
json{
  "success": true,
  "message": "Post créé avec succès",
  "data": { /* objet post */ }
}
```
4. Mettre à jour une publication
PUT /post/{id}
Body (JSON) :
```
json{
  "description": "Post modifié"
}
```
5. Supprimer une publication
DELETE /post/{id}
Réponse (200) :
```
json{
  "success": true,
  "message": "Post supprimé avec succès"
}
```
6. Liker une publication
GET /post/{id}/like
Réponse (200) :
```
json{
  "success": true,
  "message": "Post liké avec succès",
  "data": {
    "post_id": 1,
    "likes": 13
  }
}
```
7. Unliker une publication

``` GET /post/{id}/unlike   ```

8. Partager une publication

GET /post/{id}/share
Réponse (200) :
```
json{
  "success": true,
  "message": "Post partagé avec succès",
  "data": {
    "post_id": 1,
    "shares": 6
  }
}
```
9. Publications d'un utilisateur
``` GET /users/{id}/posts  ```
Réponse (200) :
```
json{
  "success": true,
  "data": [ /* liste des posts de l'utilisateur */ ]
}
 ```
Commentaires
1. Créer un commentaire
```
POST /posts/{postId}/comments
```
Body (JSON) :
```
json{
  "contenu": "Super post !",
  "user_id": 1
}
```
Réponse (201) :
```
json{
  "message": "Commentaire ajouté avec succès",
  "comment": {
    "id": 1,
    "contenu": "Super post !",
    "status": "active",
    "user_id": 1,
    "post_id": 1,
    "created_at": "2025-10-04T10:00:00.000000Z"
  }
}
```
2. Supprimer un commentaire
``` DELETE /comments/{id}  ```
Réponse (200) :
```
json{
  "message": "Commentaire supprimé avec succès"
}
```
Relations d'amitié
1. Envoyer une demande d'ami 🔒
POST /friends/request
Authorization: Bearer {token}
Body (JSON) :
json{
  "friend_id": 2
}
Réponse (201) :
```
json{
  "success": true,
  "message": "Demande d'ami envoyée avec succès",
  "data": {
    "id": 1,
    "user_id": 1,
    "friend_id": 2,
    "status": "pending",
    "sender": { /* objet user */ },
    "receiver": { /* objet user */ }
  }
}
```

2. Accepter une demande d'ami 🔒
```
POST /friends/accept/{friendshipId}
```
Authorization: Bearer {token}
Réponse (200) :
```
json{
  "success": true,
  "message": "Demande d'ami acceptée",
  "data": {
    "id": 1,
    "status": "accepted",
    /* ... */
  }
}
```
3. Rejeter une demande d'ami 🔒
POST /friends/reject/{friendshipId}
Authorization: Bearer {token}
Réponse (200) :
json{
  "success": true,
  "message": "Demande d'ami rejetée"
}

4. Liste des amis 🔒
GET /friends
Authorization: Bearer {token}
Réponse (200) :
```
json{
  "success": true,
  "count": 5,
  "data": [
    {
      "id": 2,
      "name": "Marie Curie",
      "email": "marie@example.com",
      /* ... */
    }
  ]
}
```
5. Demandes en attente 🔒
GET /friends/pending
Authorization: Bearer {token}
Réponse (200) :
```
json{
  "success": true,
  "count": 3,
  "data": [ /* liste des demandes reçues en attente */ ]
}
```
6. Supprimer un ami 🔒

``` DELETE /friends/{friendId}  ```
Authorization: Bearer {token}
Réponse (200) :
```
json{
  "success": true,
  "message": "Ami supprimé avec succès"
}
```
7. Bloquer un utilisateur 🔒

``` POST /users/{userId}/block ```

Authorization: Bearer {token}
Réponse (200) :
```
json{
  "success": true,
  "message": "Utilisateur bloqué avec succès"
}
```
8. Débloquer un utilisateur 🔒
```
DELETE /users/{userId}/unblock 
```
Authorization: Bearer {token}
Réponse (200) :
json{
  "success": true,
  "message": "Utilisateur débloqué avec succès"
}

📊 Modèles de données
User
- id (int)
- name (string)
- email (string, unique)
- password (string, hashed)
- dateOfBirth (date)
- work (string, nullable)
- photo (string, nullable)
- is_active (boolean)
- created_at (timestamp)
- updated_at (timestamp)
Post
- id (int)
- description (text)
- image (string, nullable)
- link (string, nullable)
- likes (int, default: 0)
- shares (int, default: 0)
- user_id (foreign key)
- created_at (timestamp)
- updated_at (timestamp)
Commentaire
- id (int)
- contenu (text)
- status (string, default: 'active')
- user_id (foreign key)
- post_id (foreign key)
- created_at (timestamp)
- updated_at (timestamp)
Relation
- id (int)
- user_id (foreign key)
- friend_id (foreign key)
- status (enum: 'pending', 'accepted', 'rejected', 'blocked')
- created_at (timestamp)
- updated_at (timestamp)

📝 Codes de réponse
-------------------------------------

|Code | Signification               |
|------------------------------------
|200  | Succès                      |
|------------------------------------
|201  | Créé avec succès            |
|------------------------------------
|401  | Non authentifié             |
|------------------------------------
|403  | Non autorisé                |
|------------------------------------
|404  | Non trouvé                  |
|------------------------------------
|422  | Erreur de validation        |
|------------------------------------
|500  | Erreur serveur              |
-------------------------------------

💡 Exemples d'utilisation
Exemple complet avec cURL
1. S'inscrire
bashcurl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "AWADEME Ronaldo",
    "email": "jean@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "dateOfBirth": "2001-07-26"
  }'
2. Créer un post
bashcurl -X POST http://localhost:8000/api/v1/posts \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -d '{
    "description": "Mon super post !",
    "user_id": 1
  }'
3. Envoyer une demande d'ami
bashcurl -X POST http://localhost:8000/api/v1/friends/request \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -d '{
    "friend_id": 2
  }'

📧 Support
Pour toute question ou problème, contactez :
ronaldo.awademe@epitech.eu
landry.adamaze@epitech.eu

📄 Licence
Ce projet est développé dans le cadre du projet C-DEV-160 à Epitech (Coding Academy).

Version: 1.0.0
Dernière mise à jour: Octobre 2025
