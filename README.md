# Popcorn

Application Laravel pour rechercher des films via l'API TMDb, les importer localement et gérer une watchlist personnelle.

## Installation

Installer les dépendances :

```bash
composer install
```

Créer le fichier `.env` et configurer la base de données :

```bash
cp .env.example .env
php artisan key:generate
```

Ajouter votre clé API TMDb dans le fichier `.env` :

```
TMDB_API_KEY=votre_cle_api_ici
```

Vous pouvez obtenir une clé gratuite sur https://www.themoviedb.org/settings/api

Créer la base de données et lancer les migrations :

```bash
php artisan migrate
```

Note : Laravel créera automatiquement le fichier `database/database.sqlite` si nécessaire.

## Démarrage

Lancer le serveur de développement :

```bash
php artisan serve
```

L'application sera accessible sur http://localhost:8000
