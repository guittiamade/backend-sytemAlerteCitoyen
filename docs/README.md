# Projet – Système Alerte Citoyen

Ce backend Laravel implémente l’API, l’authentification Sanctum, les entités (profils, directions, types, alertes, notifications), un service de notifications et un mini dashboard `/admin`.

## Installation locale
1. Cloner le dépôt et installer les dépendances Composer.
2. Copier `.env.example` → `.env` puis `php artisan key:generate`.
3. Base SQLite par défaut (`database/database.sqlite`).
4. `php artisan migrate --force && php artisan db:seed --force`.
5. `php artisan serve`.

## Sécurité / Rôles
- Les rôles sont enregistrés dans `profiles`. Le contrôle d’accès fin peut être ajouté via Policies/Middleware (non inclus ici pour rester concis).

## Schéma de données
- Voir les migrations dans `database/migrations`.

## API
- Voir `docs/API.md`.

