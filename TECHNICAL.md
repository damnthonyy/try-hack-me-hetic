# Documentation technique — choix d’architecture

Ce document décrit les **décisions d’architecture** et les **compromis** du dépôt. Il complète le `README.md` (démarrage et endpoints) en expliquant *pourquoi* le projet est structuré ainsi.

---

## 1. Contexte et objectifs

- Application **pédagogique** (cybersécurité / type TryHackMe) : il faut une base **lisible**, traçable et déployable **sans chaîne d’outils lourde**.
- Contrainte implicite : **PHP natif** (sans framework type Symfony/Laravel) pour que les flux HTTP, la session et l’accès base restent **explicites** dans le code.
- **Docker** comme unique environnement cible pour éviter les écarts « ça marche sur ma machine » (versions PHP, extensions, MySQL).

---

## 2. Stack d’exécution : Apache + PHP-FPM embarqué dans une image

**Choix :** image officielle `php:8.3-apache` avec `DocumentRoot` sur `public/`.

**Pourquoi :**

- Un seul conteneur **app** sert les fichiers statiques et exécute PHP : mécanisme familier (LAMP-like), configuration minimale.
- `mod_rewrite` redirige toutes les URLs vers `public/index.php` : **un seul point d’entrée** (front controller).
- Alternative non retenue ici : Nginx + PHP-FPM en deux services (plus flexible en prod, plus verbeux pour un lab).

---

## 3. Front controller et routeur minimal

**Choix :** `.htaccess` → `index.php` → `Router` (table méthode + chemin) → callable.

**Pourquoi :**

- Pas de fichier PHP par URL : la **liste des routes** est visible dans `public/index.php`, ce qui aide à la revue et aux ateliers « découvre les endpoints ».
- Les chemins sont **normalisés** (slashes, etc.) pour éviter des doublons `/login` vs `/login/`.
- **Limite assumée :** pas de paramètres dynamiques type `/user/{id}` dans le routeur actuel ; les besoins du lab restent simples.

**404 :** route inconnue → `JsonResponse::error(..., 404)` : comportement homogène pour les clients qui ne sont pas des navigateurs (curl, scripts).

---

## 4. Pas de Composer (pour l’instant) : `require_once` explicites

**Choix :** chargement des classes via `require_once` dans `index.php`, namespaces `App\...`.

**Pourquoi :**

- Zéro dépendance externe, pas d’`vendor/`, pas d’autoload à expliquer en cours.
- **Compromis :** ajouter une bibliothèque (validateur, ORM, etc.) deviendra pénible ; si le projet grossit, un **autoload PSR-4** (Composer ou simple `spl_autoload_register`) serait l’étape suivante logique.

---

## 5. Séparation `public/` / `src/` / `frontend/views/`

**Choix :**

- **`public/`** : seul répertoire exposé par Apache (`DocumentRoot`). Contient `index.php`, assets (`assets/`), uploads (`uploads/`).
- **`src/`** : logique applicative (HTTP, contrôleurs, infrastructure).
- **`frontend/views/`** : templates PHP **hors** racine web.

**Pourquoi :**

- Les vues ne sont pas servies directement par l’URL ; elles sont **incluses** après résolution de chemin dans `HtmlResponse::resolveViewPath()` (garde contre les traversées `..`).
- Réduit le risque de **fuite de fichiers** `.php` de template comparé à un dossier sous `public/` (même si une mauvaise config Apache resterait le vrai danger).

---

## 6. Double mode de réponse : HTML et JSON

**Choix :**

- **`HtmlResponse::render`** : pages « humaines » (accueil, auth, dashboard) avec layout commun.
- **`JsonResponse`** : `/health`, `/db/ping`, `/api`, erreurs globales non gérées.

**Pourquoi :**

- Le même processus sert **l’UI** et de petits **points de contrôle** utiles au Docker et au monitoring.
- **Compromis :** le contrat d’erreur est surtout JSON pour les chemins non HTML ; un navigateur qui tape une URL API reçoit du JSON, ce qui est acceptable pour ce scope.

---

## 7. Authentification : formulaires HTML, `$_POST`, redirections 302

**Choix :** `POST /login` et `POST /register` lisent **`$_POST`**, posent la session, puis **`Location:`** (succès ou `?error=`).

**Pourquoi :**

- Comportement **natif navigateur** : pas besoin de JavaScript pour l’auth ; accessibilité et simplicité.
- Les erreurs métier sont **codes dans l’URL** (`required`, `invalid`, etc.) : la vue peut afficher un message sans état côté client ni API REST dédiée.
- Alternative non retenue pour ces routes : JSON + `fetch` (plus flexible pour une SPA, mais plus de code front et de gestion d’erreurs manuelle).

**Note sécurité :** le lab peut évoluer vers des jetons CSRF ou des sessions plus strictes ; ce n’est pas documenté ici comme exigence produit.

---

## 8. Session PHP encapsulée (`App\Http\Session`)

**Choix :** wrappeur statique autour de `session_start` / `$_SESSION`, cookie **HttpOnly**, **SameSite=Lax**, durée via `SESSION_LIFETIME`.

**Pourquoi :**

- Centralise les `ini_set` et évite d’éparpiller `session_start()` dans les vues (objectif ; si une vue legacy appelle encore `session_start()`, c’est une dette technique à aligner sur ce module).
- Déconnexion : démarrer la session si besoin avant destruction, vider `$_SESSION`, expirer le cookie, `session_destroy()` — nécessaire pour que le **logout** soit effectif avec le cookie existant.

---

## 9. Accès base : PDO factory + variables d’environnement

**Choix :** `Database::createPdoFromEnv()` construit un `PDO` MySQL avec `getenv()`, `ERRMODE_EXCEPTION`, requêtes préparées pour l’auth.

**Pourquoi :**

- **Docker Compose** injecte `DB_HOST=db` : le nom du **service** résout vers le conteneur MySQL sur le réseau interne.
- Pas d’ORM : les requêtes SQL sont visibles pour l’enseignement (bon ou mauvais selon le chapitre).

---

## 10. Controleurs fins et responsabilités

**Choix :**

- **`LoginController`** : auth + redirections.
- **`DashboardController`** : garde d’accès par session, listage / upload fichiers (logique lab).
- **`HealthController`** : sonde légère.

**Pourquoi :**

- Séparer les **cas d’usage** sans multiplier les couches (pas de repository obligatoire tant que le code reste court).

---

## 11. Docker Compose : deux services, volume de données nommé

**Choix :**

- Service **`db`** : MySQL 8, persistance **`mysql_data`**, script d’init monté depuis `database/schema.sql`.
- Service **`app`** : build local, **bind mount** `./` → `/var/www/html` pour le développement (changements immédiats).

**Pourquoi :**

- Reproductibilité ; pas d’installation locale PHP/MySQL requise.
- **Piège connu :** si le volume MySQL existait **avant** l’ajout du script d’init, les migrations automatiques ne sont **pas** rejouées — il faut un `docker compose down -v` ou une application manuelle du schéma (comportement standard de l’image MySQL officielle).

---

## 12. CORS

**Choix :** middleware `Cors::handle()` en tête de `index.php`.

**Pourquoi :**

- Prépare un éventuel **front séparé** (autre origine, autre port) tout en gardant aujourd’hui une utilisation souvent **same-origin** depuis le même hôte.

---

## 13. Limites et dette technique assumées

- Routes **manuelles** dans `index.php` (pas de fichier de config dédié).
- Pas de couche **validation** réutilisable (règles dispersées dans `LoginController`).
- La vue **`dashboard.php`** peut contenir d’anciennes instructions redondantes avec `DashboardController` : risque de **duplication** et de notices PHP si session déjà démarrée.
- Le projet inclut des scénarios **volontairement vulnérables** (ex. upload) à des fins pédagogiques — ne pas confondre avec un modèle de prod.

---

## 14. Résumé en une phrase

> **Architecture « PHP nu » + Docker**, routage explicite, UI en templates serveur et auth par **POST + redirections**, complétés par quelques **endpoints JSON** utilitaires — privilégiant la **clarté** et le **contrôle pédagogique** plutôt que la stack enterprise.
