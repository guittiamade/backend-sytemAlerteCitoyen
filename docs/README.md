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
- Auth API: login via `tel` + `password`; lors de l’inscription `email` est optionnel mais `tel` est unique et obligatoire.

## Schéma de données
- Voir les migrations dans `database/migrations`.

## API
- Voir `docs/API.md`.
- Champs obligatoires notables:
  - `POST /auth/register`: `name`, `tel`, `password`
  - `POST /citoyen/alertes`: `titre`, `type_alerte_id`, `localisation`
- `types_alertes` disposent d’un champ `image` (URL) utilisé par les clients.

