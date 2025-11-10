<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Système Alerte Citoyen – Backend (Laravel 12)

API pour la plateforme de gestion des signalements urbains (Commune de Ouagadougou).

### Démarrage rapide

```bash
cp .env.example .env
php artisan key:generate
# SQLite par défaut (database/database.sqlite est déjà créé)
php artisan migrate --force
php artisan db:seed --force
php artisan serve
```

### Exécution sur le réseau local (Wi‑Fi/LAN)

Pour permettre aux membres de l’équipe (ex. Windserf) d’accéder à l’API/app depuis le même réseau, lancez le serveur lié à votre IP locale:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Depuis un autre appareil sur le même Wi‑Fi, utilisez l’IP locale de la machine hôte:

```
http://<IP_LOCALE_DE_VOTRE_PC>:8000
# Exemple: http://192.168.11.133:8000
```

Notes:
- Assurez-vous que le pare‑feu autorise les connexions entrantes sur le port 8000.
- Les appareils doivent être sur le même réseau.
- Optionnel: définir `APP_URL=http://<IP>:8000` dans `.env` pour générer des URLs correctes.

### Authentification
- Sanctum (tokens personnels)
- Endpoints: `POST /api/auth/register`, `POST /api/auth/login`, `GET /api/auth/me`, `POST /api/auth/logout`

### Emails & file d’attente
- Par défaut, si aucune config SMTP n’est fournie, les emails sont mis en file et les erreurs sont loguées.
- Pour envoyer réellement des emails, configurez dans `.env`:
  - `MAIL_MAILER=smtp`
  - `MAIL_HOST=...`, `MAIL_PORT=...`, `MAIL_USERNAME=...`, `MAIL_PASSWORD=...`, `MAIL_ENCRYPTION=tls`, `MAIL_FROM_ADDRESS=noreply@exemple.com`, `MAIL_FROM_NAME="Alerte Citoyen"`
- Démarrer le worker: `php artisan queue:listen`

### Entités principales
- Profils (rôles): citoyen, gestionnaire, direction, super_admin
- Directions (services techniques)
- Types d’alertes
- Alertes (statut: en_attente, en_cours, termine)
- Notifications (historique interne)

### Dashboard
- Route web: `/admin` affiche les KPI principaux.

## Learning Laravel

Documentation officielle: https://laravel.com/docs

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
