# YOWL

Réseau social web : publications, commentaires, likes, partages et gestion d'amis, avec un espace
d'administration. Ce dépôt contient le **frontend** (Vue 3) ; il consomme une API REST externe.

> Projet réalisé à Epitech.

## Fonctionnalités

- **Comptes** : inscription, connexion, vérification d'e-mail, réinitialisation du mot de passe ;
  routes protégées (connecté / invité / administrateur).
- **Fil d'actualité** : créer, modifier et supprimer un post, avec image (envoi vers Cloudinary) et lien.
- **Interactions** : like / unlike, partage, commentaires (ajout et suppression).
- **Recherche** : par mot-clé et filtre par hashtag, bloc « Tendances ».
- **Amis** : envoyer, accepter ou refuser une demande, voir les demandes envoyées, retirer un ami.
- **Profil** : édition des informations personnelles.
- **Administration** : liste des utilisateurs, modération des posts et commentaires, tableau de bord
  statistique (engagement, utilisateurs actifs, posts récents, top utilisateurs).
- Interface responsive avec navigation mobile dédiée.

## Stack

Vue 3 · Vite · Vue Router · Pinia (+ persistance) · Axios · Tailwind CSS · Cloudinary ·
ESLint / oxlint · Docker · GitHub Actions (installation + build à chaque push sur `main`).

## Lancer le projet

```bash
cp env.example .env      # VITE_API_URL, VITE_API_KEY
npm install
npm run dev              # http://localhost:5173
npm run build            # build de production dans dist/
```

Avec Docker :

```bash
docker build -t yowl . && docker run -p 5173:5173 yowl
```

## Structure

```
src/views/       pages (accueil, amis, profil, connexion, inscription, admin…)
src/components/  cartes de post, d'utilisateur et d'ami, barres latérales, navigation
src/stores/      PostsStore et UserStore (Pinia)
src/services/    client API (jeton Bearer) et envoi d'images
src/router/      routes et gardes d'accès
```
