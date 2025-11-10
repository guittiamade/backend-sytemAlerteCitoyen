# Documentation API – Alerte Citoyen

Base URL: `/api`

Toutes les requêtes authentifiées utilisent Sanctum avec un bearer token:
`Authorization: Bearer <token>`.

## Rôles et préfixes
- Citoyen: `/citoyen/*`
- Gestionnaire: `/gestionnaire/*`
- Direction: `/direction/*`
- Admin: `/admin/*`

## Schémas communs
- Utilisateur: `{ id, name, email, tel?, profile_id, created_at, updated_at }`
- Alerte: `{ id, titre, description?, photo?, localisation?, statut, citoyen_id, gestionnaire_id?, direction_id?, type_alerte_id, created_at, updated_at }`
- TypeAlerte: `{ id, nom, description?, created_at, updated_at }`
- Direction: `{ id, description, direction_generale?, created_at, updated_at }`

## Authentification
- POST `/auth/register`
  - Body: `{ name: string, email: string, password: string, tel?: string }`
  - 200: `{ token: string, user: Utilisateur }`

- POST `/auth/login`
  - Body: `{ email: string, password: string }`
  - 200: `{ token: string, user: Utilisateur }`

- GET `/auth/me` → Utilisateur (auth)
- POST `/auth/logout` → `{ message }` (auth)

Exemple (fetch):
```js
const res = await fetch('/api/auth/login', {
  method: 'POST', headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ email: 'citoyen@example.com', password: 'password' })
});
const { token } = await res.json();
```

## Profil utilisateur
- GET `/auth/me` (auth)
  - Retourne l’utilisateur connecté avec `profile` et `direction`.
  - curl:
  ```bash
  curl -H "Authorization: Bearer $TOKEN" \
    http://localhost:8000/api/auth/me
  ```

## Comptes par rôle (gestionnaire, direction)

> L’inscription publique (`POST /auth/register`) crée un profil `citoyen` par défaut. Pour tester les APIs `gestionnaire` et `direction`, créez ces comptes via le back-office admin.

### Créer un gestionnaire (via back office)
1. Connectez-vous au back office en super admin.
2. Menu Utilisateurs → bouton "Créer un utilisateur".
3. Renseignez: `name`, `email`, `password`, (optionnel `tel`).
4. Sélectionnez le profil: `gestionnaire`.
5. (Optionnel) Sélectionnez une `direction`.

Puis connectez-vous via l’API:
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"gestionnaire@example.com","password":"MotDePasseFort123"}'
```

### Créer un compte direction (via back office)
1. Back office → Utilisateurs → "Créer un utilisateur".
2. Renseignez `name`, `email`, `password`, etc.
3. Choisissez le profil: `direction`.
4. Choisissez la `direction` liée (obligatoire pour filtrage des alertes).

Connexion API:
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"direction@example.com","password":"MotDePasseFort123"}'
```

### Astuce (seed/tinker, optionnel)
Vous pouvez aussi créer rapidement des comptes de test via un seeder ou Tinker en assignant `profile_id` correspondant aux profils `gestionnaire` ou `direction`, et en liant `direction_id` si besoin.

- PATCH `/auth/me` (auth)
  - Met à jour partiellement le profil.
  - Body (un ou plusieurs champs):
  ```json
  {
    "name": "Nouveau Nom",
    "tel": "+22507080910",
    "email": "nouveau.mail@example.com",
    "password": "MotDePasseFort123"
  }
  ```
  - curl:
  ```bash
  curl -X PATCH http://localhost:8000/api/auth/me \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"name":"Nouveau Nom","tel":"+22507080910"}'
  ```

### Exemples concrets

#### Inscription (citoyen par défaut)
- Endpoint: `POST /api/auth/register`
- Body attendu:
```json
{
  "name": "Jean Dupont",
  "email": "jean.dupont@example.com",
  "password": "MotDePasseFort123",
  "tel": "+22501020304"
}
```
- Réponse 200 (exemple):
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJh...",
  "user": {
    "id": 12,
    "name": "Jean Dupont",
    "email": "jean.dupont@example.com",
    "tel": "+22501020304",
    "profile_id": 3,
    "created_at": "2025-11-04T12:00:00.000000Z",
    "updated_at": "2025-11-04T12:00:00.000000Z"
  }
}
```
- curl:
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Jean Dupont","email":"jean.dupont@example.com","password":"MotDePasseFort123","tel":"+22501020304"}'
```

#### Connexion
- Endpoint: `POST /api/auth/login`
- Body attendu:
```json
{
  "email": "jean.dupont@example.com",
  "password": "MotDePasseFort123"
}
```
- Réponse 200 (exemple):
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJh...",
  "user": {
    "id": 12,
    "name": "Jean Dupont",
    "email": "jean.dupont@example.com",
    "tel": "+22501020304",
    "profile_id": 3
  }
}
```
- curl:
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"jean.dupont@example.com","password":"MotDePasseFort123"}'
```

#### Utiliser le token
```bash
TOKEN="eyJ0eXAiOiJKV1QiLCJh..."; \
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/citoyen/alertes
```

#### Exemples d’erreurs
- 422 (validation):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```
- 401/403 (auth/rôle manquant):
```json
{ "message": "Unauthenticated." }
```
ou
```json
{ "message": "Forbidden" }
```

## Alertes

Les endpoints ci-dessous sont préfixés par le rôle. Exemple: `/citoyen/alertes`, `/gestionnaire/alertes`, `/direction/alertes`.

- GET `/{prefix}/alertes`
  - Query (tous rôles):
    - `statut`: `en_attente|en_cours|termine`
    - `type_alerte_id`: number
    - `q`: recherche texte (titre, description, localisation)
    - `date_from`, `date_to`: `YYYY-MM-DD`
    - `per_page`: 1..100, `page`
  - Query (supplémentaire pour gestionnaire/super_admin):
    - `direction_id`: number
  - Filtrage automatique:
    - Citoyen: uniquement ses alertes
    - Direction: uniquement sa `direction_id`
  - 200: `{ data: Alerte[], meta, links }`

- POST `/citoyen/alertes`
  - Body: `{ titre: string, description?: string, photo?: string, localisation?: string, type_alerte_id: number }`
  - 201: `Alerte`

- GET `/{prefix}/alertes/{id}` → `Alerte`

- PATCH `/{prefix}/alertes/{id}` (gestionnaire/direction)
  - Body (partiel): `{ titre?, description?, photo?, localisation?, type_alerte_id? }`
  - 200: `Alerte`

- POST `/gestionnaire/alertes/{id}/statut` et `/direction/alertes/{id}/statut`
  - Body: `{ statut: 'en_attente'|'en_cours'|'termine', direction_id?: number }`
  - 200: `Alerte`
  - Notifications & historique:
    - Passage à `en_cours` → notifie la direction + le citoyen; history `status_changed`
    - Passage à `termine` par la direction → notifie le gestionnaire pour approbation

  - Note: lorsque `statut = en_cours`, `direction_id` est **obligatoire**. Exemple d'erreur 422 si manquant:
  ```json
  {
    "message": "The given data was invalid.",
    "errors": {
      "direction_id": ["The direction id field is required when statut is en_cours."]
    }
  }
  ```

  - Exemples (gestionnaire):
  ```bash
  # Affecter à la direction 3
  curl -X POST http://localhost:8000/api/gestionnaire/alertes/1/statut \
    -H "Authorization: Bearer $TOKEN_GESTIONNAIRE" \
    -H "Content-Type: application/json" \
    -d '{ "statut": "en_cours", "direction_id": 3 }'

  # Erreur 403 rôle incorrect (token direction sur /gestionnaire/*)
  curl -X POST http://localhost:8000/api/gestionnaire/alertes/1/statut \
    -H "Authorization: Bearer $TOKEN_DIRECTION" \
    -H "Content-Type: application/json" \
    -d '{ "statut": "en_cours", "direction_id": 3 }'
  # { "message": "Accès refusé" }
  ```

Astuce: pour proposer la liste des directions côté frontend (avant l’affectation), utilisez `GET /api/directions`.

- POST `/gestionnaire/alertes/{id}/approuver`
  - Effet: confirme terminé et notifie le citoyen

Exemple (curl):
```bash
curl -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json" \
  -d '{"titre":"Nid de poule","type_alerte_id":1}' \
  http://localhost:8000/api/citoyen/alertes
```

### Exemples concrets

#### Créer une alerte (citoyen)
```bash
curl -X POST http://localhost:8000/api/citoyen/alertes \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "titre":"Feu de poubelle",
    "description":"Gros dégagement de fumée",
    "localisation":"Rue des Palmiers",
    "type_alerte_id": 2
  }'
```

Payloads possibles:
- Minimal (requis):
```json
{ "titre": "Nid de poule", "type_alerte_id": 1 }
```
- Complet:
```json
{
  "titre": "Incendie de poubelle",
  "description": "Forte fumée derrière le marché",
  "photo": "https://exemple.com/photos/incendie_123.jpg",
  "localisation": "Quartier Plateau, rue des Palmiers",
  "type_alerte_id": 2
}
```
- Avec localisation simple:
```json
{
  "titre": "Lampadaire en panne",
  "description": "Aucune lumière depuis 3 jours",
  "localisation": "Avenue de la Paix, poteau n°17",
  "type_alerte_id": 3
}
```
- Exemple voirie:
```json
{
  "titre": "Trottoir endommagé",
  "description": "Plaques disjointes, dangereux pour piétons",
  "photo": "https://exemple.com/photos/trottoir_456.jpg",
  "localisation": "Boulevard Lagune, devant la pharmacie",
  "type_alerte_id": 4
}
```
- Erreurs fréquentes à éviter:
```json
{ "titre": "", "type_alerte_id": 9999 }
```
`titre` est requis non vide; `type_alerte_id` doit exister.

#### Changer le statut (gestionnaire)
```bash
curl -X POST http://localhost:8000/api/gestionnaire/alertes/123/statut \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{ "statut":"en_cours", "direction_id": 5 }'
```

#### Approuver la résolution (gestionnaire)
```bash
curl -X POST http://localhost:8000/api/gestionnaire/alertes/123/approuver \
  -H "Authorization: Bearer $TOKEN"
```

#### Filtres utiles (liste des alertes)
- Recherche texte + période:
```bash
curl -H "Authorization: Bearer $TOKEN" \
  'http://localhost:8000/api/gestionnaire/alertes?q=voirie&date_from=2025-11-01&date_to=2025-11-30&per_page=20&page=1'
```

- Par type d’alerte:
```bash
curl -H "Authorization: Bearer $TOKEN" \
  'http://localhost:8000/api/gestionnaire/alertes?type_alerte_id=2'
```

- Par direction (gestionnaire/super_admin):
```bash
curl -H "Authorization: Bearer $TOKEN" \
  'http://localhost:8000/api/gestionnaire/alertes?direction_id=5&statut=en_cours'
```

#### Exemple de réponse paginée (annoté)
```json
{
  "data": [
    { "id": 101, "titre": "Nid de poule", "statut": "en_cours", "type_alerte_id": 1, "created_at": "2025-11-04T10:11:12Z" }
  ],
  "links": {
    "first": "http://localhost:8000/api/gestionnaire/alertes?page=1",
    "last": "http://localhost:8000/api/gestionnaire/alertes?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/gestionnaire/alertes?page=2"
  },
  "meta": {
    "current_page": 1,    // page courante
    "from": 1,            // index du 1er élément retourné
    "last_page": 5,       // nombre total de pages
    "path": "http://localhost:8000/api/gestionnaire/alertes",
    "per_page": 20,       // éléments par page
    "to": 20,             // index du dernier élément retourné
    "total": 100          // nombre total d’éléments
  }
}
```

## Directions
- GET `/directions` → `Direction[]` (auth)
  - Exemple (curl):
  ```bash
  curl -H "Authorization: Bearer $TOKEN" \
    http://localhost:8000/api/directions
  ```
  - Exemple de réponse:
  ```json
  [
    { "id": 1, "description": "Voirie et Assainissement", "direction_generale": "DGST" },
    { "id": 2, "description": "Hygiène et Salubrité", "direction_generale": "DGST" },
    { "id": 3, "description": "Sécurité et Police Municipale", "direction_generale": "DGSP" }
  ]
  ```
- POST `/directions` `{ description, direction_generale? }` (admin)
- PUT `/directions/{id}` (admin)
- DELETE `/directions/{id}` (admin)

## Types d’alertes
- GET `/types-alertes` → `TypeAlerte[]` (auth)
- POST `/types-alertes` `{ nom, description? }` (admin)
- PUT `/types-alertes/{id}` (admin)
- DELETE `/types-alertes/{id}` (admin)

## Notifications (in-app)
- GET `/notifications`
  - Query: `statut` (0/1 = non lue/lue), `per_page`, `page`
  - Par défaut, les notifications sont créées en **non lues** (`statut=false`) et `date_envoi=null`.
  - Utilisez `PATCH` pour les marquer **lues**: `statut=true` met aussi `date_envoi=now()`.
  - 200: `{ data: Notification[], meta, links }` (uniquement les notifications de l’utilisateur connecté)

- PATCH `/notifications/{id}`
  - Body: `{ statut: boolean }` (marquer lu/envoyé)
  - 200: `Notification`

### Exemples concrets

#### Lister mes notifications
```bash
curl -H "Authorization: Bearer $TOKEN" \
  'http://localhost:8000/api/notifications?per_page=10&statut=0'
```

#### Marquer une notification comme lue
```bash
curl -X PATCH http://localhost:8000/api/notifications/456 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"statut": true}'
```

## Erreurs & conventions
- 422 Validation: `{ message, errors: { field: [msg] } }`
- 401/403: authentification/autorisation manquante
- Les dates sont ISO 8601 (UTC par défaut)

## Pagination
- Utiliser `per_page` (1..100) et `page`.
- Les réponses paginées contiennent `data`, `meta`, `links`.

## Workflow de statut
`en_attente` → `en_cours` → `termine`
- Affectation à une direction via `direction_id` possible lors du passage en `en_cours`.

## Notes d’intégration
- Pagination: utilisez `page` (query) et lisez `meta`/`links` de la réponse.
- Upload photo: champ `photo` prévu sous forme d’URL; si besoin de multipart upload, on pourra exposer un endpoint d’upload.
- Notifications: enregistrées en base (`notifications_custom`) lors de la soumission et du changement de statut.

## Statistiques

- Citoyen: `GET /citoyen/stats`
  - 200:
  ```json
  { "total": 12, "en_attente": 5, "en_cours": 4, "termine": 3 }
  ```

 - Gestionnaire: `GET /gestionnaire/stats`
  - 200:
  ```json
  { "assignes": 18, "en_cours": 7, "termine": 9 }
  ```

- Direction: `GET /direction/stats`
  - 200:
  ```json
  { "receptionnes": 25, "en_cours": 10, "termine": 12 }
  ```

- Admin: `GET /admin/stats`
  - 200:
  ```json
  { "total": 120, "en_attente": 30, "en_cours": 50, "termine": 40 }
  ```

## Versioning (optionnel)
- Préfixe recommandé: `/api/v1` pour stabiliser les contrats.
