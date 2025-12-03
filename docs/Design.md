# Design UI/UX – Alerte Citoyen (Flutter mobile & web)

Ce document décrit la structure visuelle, les composants, les interactions et les bonnes pratiques pour développer l’interface en Flutter, destinée aux rôles citoyen, gestionnaire, direction et admin (web existant). Objectif: servir de référence aux maquettes et guider l’intégration des APIs.

## Principes généraux
- **Framework**: Flutter (Material 3, theming dynamique, responsive/adaptatif Web + Mobile).
- **Responsive**: Layouts fluides avec breakpoints:
  - Mobile: <600px (handset, scrolling vertical, 1 colonne)
  - Tablet: 600–1024px (2 colonnes, navigation rail)
  - Desktop/Web: >1024px (sidebar + contenu + panneau secondaire optionnel)
- **Accessibilité**: contrastes AA, tailles de police adaptatives, zones tactiles min 44px, focus visible.
- **Performances**: pagination côté API, chargements progressifs, debounce sur recherche, caching local (par ex. Riverpod/Bloc + caching mémoire).
- **Sécurité**: token Bearer (Sanctum) dans headers; gestion d’expiration; masquer actions non autorisées.

## Navigation et structure
- **Shell**: `Scaffold` avec `AppBar` (titre, actions, badge notifications) + navigation adaptative:
  - Mobile: `BottomNavigationBar` (Dashboard, Alertes, Notifications, Profil)
  - Tablet: `NavigationRail`
  - Web/Desktop: `NavigationDrawer`/Sidebar persistante
- **Routing**: `go_router` recommandé pour URL lisibles et deep-linking. Préfixes par rôle dans la logique (pas dans l’URL), l’API gère les rôles.
- **États**: Gestion via `Riverpod` ou `Bloc` (recommandé: Riverpod + Freezed + Dio/Chopper).

## Écrans par rôle

### Citoyen (Mobile priorité, Web support)
- **Dashboard**: cartes KPI (mes alertes par statut), bouton “Nouvelle alerte”.
  - KPIs depuis `GET /citoyen/stats`: `total`, `en_attente`, `en_cours`, `termine`.
- **Mes alertes (liste)**:
  - Filtres: `q`, `type_alerte_id`, `statut`, `date_from`, `date_to`.
  - Table/tiles avec: titre, type, statut (badge), date.
  - Pagination: boutons Suivant/Précédent (ou infinite scroll optionnel).
- **Créer une alerte (formulaire)**:
  - Champs: `titre` (requis), `description`, `photo` (URL), `localisation`, `type_alerte_id` (select depuis `/directions`? non, depuis `/types-alertes`).
  - Retour visuel validation (422) sur champs concernés.
- **Détail alerte**: informations, statut (badge), type, direction affectée, timeline historique.
- **Notifications**: liste avec badge non lues; action “Marquer lue”.

### Gestionnaire (Mobile & Web)
- **Dashboard**: KPIs globales, repartition par statut/type/direction.
  - KPIs depuis `GET /gestionnaire/stats`: `assignes`, `en_cours`, `termine`.
- **Alertes (liste)**:
  - Filtres complets + `direction_id`.
  - Actions rapides: `Affecter` (statut → en_cours + select `direction`), `Voir`, `Approuver` (si en fin de cycle).
- **Détail alerte**:
  - Panneau d’actions: Affecter une direction (sélecteur `/directions`), Approuver la résolution.
  - Timeline: created → en_cours (avec direction) → termine → approuvée (si applicable).

### Direction (Mobile & Web)
- **Dashboard**: alertes de ma direction, priorisées par date/statut.
  - KPIs depuis `GET /direction/stats`: `receptionnes`, `en_cours`, `termine`.
- **Alertes (liste)**: filtrées automatiquement par `direction_id` (côté API), filtres usuels.
- **Détail alerte**: bouton “Marquer terminé” → POST statut `termine`.

### Admin (Web existant)
- S’aligner visuellement (couleurs, typos) pour cohérence.

## Composants UI
- **Badges de statut**: `en_attente` (surfaceVariant/outline), `en_cours` (primary), `termine` (success/green).
- **Listes**: `ListView.builder` (mobile), `PaginatedDataTable`/`DataTable2` (web) selon besoin.
- **Formulaires**: `TextFormField`, `DropdownButtonFormField`, `DatePicker`, validation côté client alignée avec règles API.
- **Filtres**: barre dédiée (web) / `BottomSheet` (mobile), état synchronisé avec URL (web) et mémoire navigation (mobile).
- **Timeline**: composant vertical listant événements (création, status_changed, approbation), horodatage.
- **Notifications**: `Badged` sur icône cloche, `ListTile` avec action marquer lue.
- **Types d’alertes**: chaque type possède un champ `image` (URL). Utiliser cette miniature dans les listes, formulaires et grilles pour aider l’identification rapide (icône ronde 40x40 ou chip illustrée).
- **Feedback**: `SnackBar` pour succès/erreurs, `CircularProgressIndicator`/skeletons.

## Intégration API (contract mapping)
- Base URL: `/api` + header `Authorization: Bearer <token>`.
- **Auth**
  - POST `/auth/login` → { token, user }
  - GET `/auth/me`, PATCH `/auth/me`, POST `/auth/logout`
- **Référentiels**
  - GET `/directions` (pour select d’affectation)
  - GET `/types-alertes` (pour formulaire de création & filtres)
- **Alertes**
  - GET `/{prefix}/alertes` (citoyen/gestionnaire/direction) avec filtres/pagination
  - POST `/citoyen/alertes` (création)
  - GET `/{prefix}/alertes/{id}` (détail)
  - PATCH `/{prefix}/alertes/{id}` (edit partiel)
  - POST `/gestionnaire/alertes/{id}/statut` (affecter en `en_cours` avec `direction_id`), `/direction/alertes/{id}/statut` (changer statut)
  - POST `/gestionnaire/alertes/{id}/approuver` (approuver résolution)
- **Notifications**
  - GET `/notifications?statut=0|1&per_page=&page=`
  - PATCH `/notifications/{id}` body `{statut:true}` (marquer lue → `date_envoi=now`) 

## États et erreurs
- 401/403: rediriger vers écran login ou afficher modal “Accès refusé”.
- 422: afficher messages par champ (validation server-driven).
- Réseau: écrans d’erreur avec retry; loaders pendant requêtes.

## Theming & style
- **Material 3** avec schéma de couleurs personnalisé:
  - Primary: `#2B6CB0` (bleu)
  - Secondary: `#38B2AC` (turquoise)
  - Success (custom): `#16A34A`
  - Warning: `#D97706`
  - Error: `#DC2626`
- **Typographie**: tailles 14–16 base, titres hiérarchisés (H1→H6), interlignage 1.4–1.6.
- **Iconographie**: Material Symbols (ou Lucide via packages Flutter équivalents), icônes statut/action cohérentes.

## Spécificités mobiles (gestionnaire & direction)
- Écrans optimisés une main:
  - CTA principaux en bas (FAB ou BottomBar actions).
  - Filtres via `BottomSheet`/`ModalBottomSheet`.
- Listes de cartes compactes, glisser pour actions rapides (marquer terminé/affecter).
- Notifications en push (option futur) + in-app list.

## Spécificités web
- Layout 3 colonnes possible (Sidebar / Contenu / Panneau détails).
- Tables paginées, colonnes configurables, sticky header.
- URL avec query params synchronisés aux filtres.

## Flux critiques
- **Affectation (gestionnaire)**: 
  1) Ouvrir modal → sélectionner `direction` (GET `/directions`),
  2) POST `/gestionnaire/alertes/{id}/statut` body `{statut:"en_cours", direction_id}`,
  3) Rafraîchir ligne et timeline; afficher toast.
- **Terminer (direction)**: POST `/direction/alertes/{id}/statut` `{statut:"termine"}`.
- **Approuver (gestionnaire)**: POST `/gestionnaire/alertes/{id}/approuver`.
- **Notifications**: GET liste, PATCH `{statut:true}` à l’action de lecture, MAJ badge.

## Données et modèles (côté client)
- **Types** alignés aux schémas API: `Utilisateur`, `Alerte`, `TypeAlerte`, `Direction`, `Notification`.
- Recommandé: générer modèles immuables (`freezed`) + `fromJson/toJson` (`json_serializable`).

## Checklist QA
- Rôles: vérification des actions affichées selon `profile.nom`.
- Filtres: persistence état, compatibilité mobile.
- Workflow: transitions valides uniquement (`en_attente → en_cours → termine`).
- Notifications: badge exact, PATCH met bien à jour liste & compteur.
- Accessibilité: navigation clavier/web, labels, contrastes.

## Roadmap UI
- V1: Citoyen mobile + web essentiel; Gestionnaire/Direction mobile; Notifications; Filtres/Pagination.
- V1.1: Timelines enrichies, exports, favoris.
- V2: Upload photo natif (endpoint multipart), push notifications, thèmes dynamiques.

---
Dernière mise à jour: auto-généré pour alignement initial. Ajuster couleurs/brand si charte disponible.

## Wireframes textuels (par rôle)

Ces schémas textuels servent de base aux maquettes Flutter. Ils décrivent l’agencement principal sur Mobile (M) et Web (W).

### Citoyen
- M – Dashboard
  - AppBar: Titre | Icône Notifications (badge)
  - Contenu: Cartes KPI (Mes alertes par statut)
  - FAB: “Nouvelle alerte”
- M – Mes alertes (liste)
  - Header: Champ recherche + bouton Filtres (BottomSheet)
  - Liste: cartes (Titre, Type, Statut, Date)
  - Footer: Pagination/infinite scroll
- M – Créer alerte (form)
  - Champs: Titre*, Description, Photo URL, Localisation, Type (select)
  - Actions: Annuler | Soumettre
- M – Détail alerte
  - En-tête: Titre + Badge statut
  - Corps: Type, Direction affectée, Localisation, Description
  - Section: Timeline (créé → en_cours → terminé)
- W – Navigation Drawer (Mes alertes, Nouvelle alerte, Notifications, Profil)
  - Page Liste: Filtres latéraux + Table/list avec colonnes et pagination

### Gestionnaire
- M – Dashboard
  - KPIs: Total, En attente, En cours, Terminées
  - Raccourcis: “Alertes à affecter”
- M – Alertes (liste)
  - Barre: Recherche + Filtres (incl. direction)
  - Cartes: Titre, Type, Statut, Direction, Date
  - Actions rapides (slide): Affecter | Voir | (si applicable) Approuver
- M – Détail alerte
  - En-tête: Titre + Statut
  - Bloc actions: Sélecteur Direction + bouton “Affecter (en_cours)”
  - Si statut terminé: bouton “Approuver la résolution”
  - Timeline
- W – Sidebar: Dashboard, Alertes, Notifications
  - Page Alertes: Filtres en haut (q, dates, type, direction, statut)
  - Tableau: colonnes + menu action ligne (Affecter, Voir, Approuver)

### Direction
- M – Dashboard
  - KPIs: À traiter (en_cours), Récentes, Terminées
- M – Alertes (liste)
  - Barre: Recherche + Filtres (type, dates, statut)
  - Cartes: Titre, Type, Statut, Date
  - Action ligne (si non terminé): “Marquer terminé”
- M – Détail alerte
  - En-tête: Titre + Statut
  - Bouton principal: “Marquer terminé”
  - Timeline
- W – Layout similaire gestionnaire mais filtré automatiquement par `direction_id`

### Notifications (tous rôles)
- M: Page liste (non lues en haut), action “Marquer lue” (PATCH)
- W: Icône cloche dans AppBar avec badge, liste paginée dédiée

### Profil (tous rôles)
- M/W: Formulaire partiel (name, tel, email, password) en PATCH `/auth/me`
