# Suivi des corrections — HAKI YETU (Symfony 7.4)

Session du **28 octobre 2026**. Ce document regroupe :

1. l'analyse et les **propositions** faites au début de la session ;
2. tout ce qui a été **corrigé** depuis (étapes 1, 2 et 3) ;
3. ce qui **reste à faire**.

> **État git** : les corrections ne sont pas commitées. Elles sont dans la copie de travail principale (`git status` pour les voir).
> **Sauvegarde de la base** d'avant les migrations : `var/data.db.bak-202610280818`.

---

## Vue d'ensemble

| Étape | Contenu | État |
|---|---|---|
| 1 | Points bloquants (§1 de l'analyse) | ✅ Terminé et testé |
| 2 | Sécurité et données personnelles (§2) | ✅ Terminé et testé |
| 3 | Bugs fonctionnels (§3) | 🟡 Code écrit, **migration non appliquée, pas de rapport final** |
| 4 | Regrouper l'administration dans EasyAdmin (§4) | ⏳ À faire |
| 5 | Accessibilité et front-end (§5) | ⏳ À faire |
| 6 | Tests, PHPStan, nouvelles fonctionnalités (§6) | ⏳ À faire |

---

# Partie A — Analyse et propositions initiales

Les dépendances sont à jour : Symfony 7.4.19 LTS, Doctrine ORM 3.7, EasyAdmin 4.29, PHP 8.3, et `composer audit` ne signale rien. Le problème n'était donc pas les versions, mais plusieurs fonctionnalités clés cassées et deux fuites de données personnelles.

Vérifications faites : vraies requêtes sur l'application, `lint:twig`, `doctrine:schema:validate`, état des migrations.

## 🔴 §1. Bloquant : ce qui plantait (testé)

| # | Problème | Où | Effet |
|---|---|---|---|
| 1 | Le tableau de bord compte `Rapport.public`, champ inexistant | `src/Controller/Admin/DashboardController.php:63` | `/admin` plante : back-office inaccessible |
| 2 | CRUD Rapport : champs inexistants (`activites`, `public`, `actif`) et template absent (`admin/field/pdf_preview.html.twig`) | `src/Controller/Admin/RapportCrudController.php:63-79` | Gestion des rapports en panne |
| 3 | La table `don` n'existe pas : aucune migration ne la crée | `migrations/` | Chaque don → erreur 500 |
| 4 | Pas de migration pour `offre_emploi` ni `candidature` (tables créées à la main) | `migrations/` | Serveur neuf : emplois et candidatures en panne |
| 5 | `Email` au lieu de `TemplatedEmail` | `src/Controller/ContactController.php:40` | Formulaire de contact plante |
| 6 | `/urgences/new` déclaré après `/urgences/{id}` | `src/Controller/UrgenceController.php` | 404 : impossible de créer une urgence |
| 7 | `PartenaireType` utilise `ordreAffichage`, l'entité a `ordre` | `src/Form/PartenaireType.php:90`, templates `partenaires/*` | Formulaires partenaires plantent |
| 8 | 4 templates ne compilent pas (filtre `string` inexistant, `rapport.type\|capitalize ?? ...` invalide) | `don/confirmation`, `don/show`, `don/edit`, `rapports/show:142` | Erreur 500 sur ces pages |
| 9 | Deux pare-feux séparés (`admin` sur `^/admin`, `main` ailleurs) | `config/packages/security.yaml` | Admin non reconnu sur `/don/admin`, `/partenaires/admin`, `/rapports/new`, `/urgences/*/edit`, `/relation-media/new` |
| 10 | Template `emails/candidature_notification.html.twig` absent | `src/Controller/CandidatureController.php:56` | Aucun e-mail envoyé lors d'une candidature, erreur avalée |

## 🔴 §2. Sécurité et données personnelles

- **CV, lettres et pièces des candidats en accès public** dans `public/uploads/candidatures/`, noms prévisibles. → Stocker hors de `public/` et servir via un contrôleur réservé à l'admin.
- **Fuite des données des donateurs** : `/don/confirmation/{id}` affiche nom et e-mail avec un id séquentiel. → Jeton aléatoire ou id en session.
- **XSS** : `{{ annonce.contenu|raw }}` dans `annonces/show.html.twig`, et logos partenaires en **SVG** acceptés. → Retirer `|raw` et le SVG.
- **Aucun anti-spam** sur contact, dons, candidatures (alors que `symfony/rate-limiter` est installé). → Champ piège + limite par IP.
- **Adresses et secrets en dur ou versionnés** : Gmail personnel dans `CandidatureController` et `.env`, secret de `.env.dev` versionné, `APP_SECRET` d'exemple. → `.env.local` et `secrets:set` en production.
- **Expéditeur `noreply@hakiyetuasbl.cd` via le SMTP Yahoo** : e-mails rejetés (SPF/DMARC).
- **`AdminUserCrudController`** : un admin peut se donner tous les rôles, supprimer son propre compte ou le dernier admin.
- Une image `certificat-daptitude-physique…jpg` dans la galerie publique : possible document médical publié par erreur.
- Les fixtures `RelationMediaFixtures` inventent des articles attribués à RFI, ONU Info, Le Potentiel : **ne jamais les charger en production**.

## 🟠 §3. Bugs fonctionnels (logique)

- **Dons** : faux message « e-mail de confirmation envoyé » (`envoyerEmailConfirmation()` vide) ; montant en `float` au lieu de `DECIMAL` ; `MoneyType` affiche « USD » quelle que soit la devise ; statistiques codées à `0`.
- **Urgences → Dons** : les paramètres `urgence` et `montant_suggestion` ne sont pas utilisés, `Don` n'a pas de lien vers `Urgence`, donc `montantCollecte` ne bouge jamais.
- **`Urgence::getJoursRestants()`** : `diff()->days` toujours positif, une urgence terminée affiche des jours restants.
- **Annonces** : chaque modification remet à zéro la date d'expiration (logique dupliquée dans l'entité et le CRUD).
- **Offres d'emploi** : slug écrasé et non unique ; description `NotBlank` mais facultative dans l'admin ; `ImageField` pour un PDF.
- **Supprimer une offre avec candidatures** : erreur de clé étrangère ; `removeCandidature()` met `null` sur une colonne non nullable.
- **Activités** : slug `unique` sans gestion des doublons → erreur 500.
- **Candidatures (admin)** : arguments de `sprintf` inversés, HTML affiché échappé, candidatures modifiables.
- **Messages (admin)** : `createdAt` dans le formulaire sans setter → erreur à l'enregistrement.
- **Partenaires (admin)** : logo obligatoire même en modification, `ordre` en `TextField`, aide « 5 Mo » alors que l'entité autorise 2 Mo.
- **Rapports** : aide « PDF 20 Mo », l'entité autorise 10 Mo avec Word et Excel.
- **`RelationMedia::getShortDescription()`** : `substr` au lieu de `mb_substr` (accents coupés).
- **`Volontaire`** : entité, dépôt et table sans formulaire, page ni menu admin (code mort).
- **DQL `YEAR`** configuré pour MySQL alors que la base est SQLite ; `MONTH` non déclaré.

## 🟠 §4. Architecture et technique

- **Deux back-offices en parallèle** (EasyAdmin + pages « admin » faites à la main). → Tout passer dans EasyAdmin (`DonCrudController`, `UrgenceCrudController`, `VolontaireCrudController`) et supprimer les routes publiques `/xxx/admin`, `/new`, `/edit`.
- **Migrations** écrites en SQL propre à SQLite, alors que la production prévoit MySQL ; schéma non synchronisé. → Passer en MySQL en local et régénérer une migration propre.
- **Dates `updatedAt`** gérées à la main. → `#[ORM\HasLifecycleCallbacks]` / `#[PreUpdate]` ou trait `Timestampable`.
- **Statuts en texte libre** répétés partout. → Enums PHP (`enum StatutDon: string`) avec `enumType`.
- **`changerStatut`** accepte n'importe quel statut venant de l'URL.
- **Pas de tests ni d'analyse statique.** → PHPUnit (tests fonctionnels) et PHPStan.
- `error_log()` au lieu de `LoggerInterface`.
- `translation.yaml` en `default_locale: en` → passer en `fr`.
- `debug.yaml` utilise `VAR_DUMPER_SERVER` non défini.
- `templates/test_integration.html.twig` à supprimer.
- Équipe et objectifs codés en dur dans `AboutController` → entité `MembreEquipe`.
- `render(controller(...))` dans le footer → Twig Component.

## 🟡 §5. Front-end, accessibilité, performance

**Accessibilité** (critique pour une association de défense des droits des personnes handicapées) :
- Menus déroulants ouverts uniquement au survol (`group-hover`), inaccessibles au clavier et aux lecteurs d'écran, pas d'`aria-expanded`. → `group-focus-within` + contrôleur Stimulus.
- Viser **WCAG 2.1 AA** : contrastes, focus visibles, `alt` dans la galerie, agrandissement du texte, mode contraste élevé.

**Bugs front** :
- `class "fit-picture"` invalide (`base.html.twig:86` et `:268`).
- `form-styles.css` chargé via `asset()` alors qu'il est dans `assets/` et utilise `@apply` → jamais chargé. → L'importer dans `app.js`.
- Stimulus démarré deux fois (`bootstrap.js` et `stimulus_bootstrap.js`), `enableStimulusBridge` appelé deux fois dans `webpack.config.js`.
- Turbo actif mais `app.js` utilise `DOMContentLoaded` → menu mobile cassé après la première navigation.
- `CandidatureController` passe `createView()` : pas de statut 422, erreurs non affichées avec Turbo.
- Pas de `favicon.ico`, `sameAs` provisoire, images Unsplash sur la page Don.

**Performance** (connexion faible à Uvira) :
- Google Fonts et Font Awesome complet via CDN → héberger localement, ne garder que les icônes utilisées.
- Images non converties en WebP → `format: webp` dans les filtres Liip.

## 💡 §6. Propositions fonctionnelles pour la mission

1. **Signalement de violations de droits** : formulaire confidentiel, éventuellement anonyme, suivi des dossiers dans l'admin, données chiffrées.
2. **Paiement Mobile Money réel** (FlexPay, MaxiCash, CinetPay…) avec confirmation automatique et reçu PDF par e-mail.
3. **Page « Devenir volontaire »** (l'entité `Volontaire` existe déjà).
4. **Site multilingue** français, swahili, anglais.
5. **Newsletter** (la case du formulaire de don n'est reliée à rien).
6. **Témoignages de bénéficiaires**, avec consentement explicite.
7. **Notifications admin** (nouveau don, candidature, message) via Messenger en asynchrone.

## Ordre de correction recommandé

1. Points 1 à 10 du §1.
2. §2 Sécurité.
3. Regrouper toute l'administration dans EasyAdmin.
4. Accessibilité du menu, puis les autres corrections front.
5. Tests et PHPStan, puis les nouvelles fonctionnalités.

> Note : en pratique, l'étape 3 réalisée a porté sur les **bugs fonctionnels du §3** (et non sur EasyAdmin).

---

# Partie B — Ce qui a été fait

## ✅ Étape 1 — Points bloquants (§1)

| # | Correction |
|---|---|
| 1 | **Tableau de bord `/admin`** : compte tous les rapports (le champ `public` n'existe pas). |
| 2 | **CRUD Rapport** : champs inexistants retirés, template manquant remplacé par un lien « Voir », ajout des champs `période` et `langue`, fichier facultatif en modification. |
| 3-4 | **Nouvelle migration** `migrations/Version20261028090000.php` : crée `don`, `offre_emploi` et `candidature` si elles manquent. Compatible SQLite et MySQL (SQL MySQL vérifié). **Appliquée** sur la base locale. |
| 5 | **Contact** : envoi via `TemplatedEmail`, plus de plantage. |
| 6 | **Routes** : `{id}` limité aux chiffres (`\d+`) → `/urgences/new` fonctionne. Même protection sur dons, rapports, annonces, partenaires et médias. |
| 7 | **Partenaires** : `ordreAffichage` → `ordre` (formulaire et templates), ajout de « Afficher dans le carrousel ». EasyAdmin : logo facultatif en modification, `ordre` numérique. |
| 8 | **Templates Twig** : 6 erreurs corrigées, les 44 fichiers compilent. |
| 9 | **Un seul pare-feu** : l'admin est reconnu partout et revient sur la page demandée après connexion. |
| 10 | **Notification de candidature** : template `emails/candidature_notification.html.twig` créé ; destinataire lu depuis `CONTACT_RECIPIENT_EMAIL` (plus de Gmail en dur) ; erreurs journalisées. |
| + | **Bug trouvé pendant les tests** : la modification d'un don plantait (champs `statut` et `référence de paiement` absents). Option `admin` ajoutée à `DonType` : ces champs n'apparaissent qu'en mode admin. |

**Vérifié** sur une copie de la base avec un admin temporaire : connexion, 13 pages admin en 200, don créé → confirmé → payé, message de contact, partenaire créé. Sur la vraie base : toutes les pages publiques en 200, conteneur et mapping Doctrine OK.

## ✅ Étape 2 — Sécurité et données personnelles (§2)

1. **Documents des candidats protégés** : les 15 fichiers (6 candidats) déplacés dans `var/uploads/candidatures/`, hors du public. Ouverture uniquement depuis le back-office connecté, sans cache navigateur. Liens « Ouvrir » réparés, candidatures en **lecture seule** dans l'admin.
2. **Confirmation de don privée** : `/don/confirmation/{id}` supprimé ; le don est gardé dans la session du donateur. Un autre visiteur est redirigé vers `/don/`. Le faux message « e-mail envoyé » est remplacé par un texte exact.
3. **XSS fermées** : `|raw` retiré des annonces, SVG refusé pour les logos partenaires.
4. **Anti-spam** sur contact, don et candidature : champ piège invisible (`src/Form/HoneypotType.php`, `src/Security/AntiSpam.php`, ignoré par les lecteurs d'écran) + **5 envois max par quart d'heure et par IP** (`config/packages/rate_limiter.yaml`).
5. **Comptes admin** : champ « rôles » retiré, impossible de supprimer son propre compte ou le dernier admin, mot de passe de 8 caractères minimum.
6. **`.env`** : pointe de nouveau vers `hakiyetuass@yahoo.fr`. Le Gmail personnel est dans `.env.local` (ignoré par git).

**Vérifié** sur une copie de la base :

| Test | Résultat |
|---|---|
| Ouvrir un CV sans être connecté | refusé |
| Ouvrir un CV en tant qu'admin | 200, PDF |
| Confirmation de don, même visiteur | 200 |
| Confirmation de don, autre visiteur | redirigé vers `/don/` |
| Ancienne URL `/don/confirmation/1` | n'existe plus |
| Contact avec champ piège rempli | refusé, rien enregistré |
| 6e envoi de contact en moins de 15 min | bloqué |

Non testé : le blocage de la suppression de son propre compte admin (logique en place, clic non simulé).

## 🟡 Étape 3 — Bugs fonctionnels (§3) — en cours

Le code a été écrit, mais l'étape s'est arrêtée pendant le test de la migration : **pas de test complet ni de rapport final**.

**Dons et urgences**
- `Don.montant` et les montants d'`Urgence` passent en **DECIMAL**.
- Lien optionnel `Don → Urgence` (`onDelete: SET NULL`).
- `src/EventListener/DonUrgenceListener.php` : le montant collecté d'une urgence se met à jour tout seul avec les dons **CDF payés** (ajout et retrait).
- Date de paiement remplie automatiquement quand le statut passe à « payé » (y compris depuis le formulaire d'édition).
- `Urgence::getJoursRestants()` corrigé (plus de jours restants pour une urgence terminée).
- `DonType` : plus de devise USD imposée ; saisie en décimal.
- Statistiques **par devise** (`DonRepository`, `DonController`), pourcentages calculés dans le contrôleur (plus de division par zéro), `don/admin` affiche les totaux par devise.
- `changerStatut` n'accepte plus qu'une liste fermée de statuts.
- Pré-remplissage du don depuis une urgence, bandeau « ce don soutient une urgence » sur la page publique, champ « Appel d'urgence lié » dans l'édition admin.
- Montants suggérés de la page Don adaptés à la devise (plus de « 10 USD » pour une urgence en CDF).
- `UrgenceType` : aide indiquant que le montant collecté est automatique.

**Annonces**
- La date d'expiration n'est plus remise à zéro à chaque modification (entité + `AnnonceCrudController`).
- Une annonce désactivée n'est plus visible sur le site public.

**Slugs, activités, offres d'emploi**
- Nouveau service `src/Service/SlugGenerator.php` : slugs uniques (`titre`, `titre-2`, `titre-3`…) pour activités et offres.
- `Activite::setSlug()` accepte un slug vide (plantait avant).
- CRUD des offres réécrit : slug, description obligatoire, PDF affiché comme un lien.
- Slug des offres unique en base.
- Offres désactivées masquées sur le site public.
- Relation offre → candidatures en `orphanRemoval` (suppression d'une offre avec candidatures).

**Divers**
- `MessageCrudController` : `createdAt` masqué dans le formulaire.
- `RelationMedia::getShortDescription()` : `mb_substr` (accents préservés).
- `doctrine.yaml` : déclaration DQL `YEAR` (MySQL) retirée.

**Migration** : `migrations/Version20261028120000.php` (« Montants en DECIMAL, lien don-urgence, slug unique des offres »), écrite avec l'API Schema pour SQLite et MySQL. ⚠️ **Elle n'est pas encore appliquée** sur la base locale (`doctrine:migrations:status` : 7 exécutées sur 8). Le test montée/descente/remontée sur une copie (`var/tmp-tests/`) a été lancé mais son résultat n'a pas été relevé.

**Pas encore traité dans le §3** (à vérifier) : l'e-mail de confirmation des dons (`envoyerEmailConfirmation()`), l'entité `Volontaire` (code mort), la déclaration `MONTH` pour les statistiques mensuelles, les textes d'aide des tailles de fichiers (partenaires 5 Mo / 2 Mo, rapports 20 Mo / 10 Mo).

---

# Partie C — Reste à faire

## Pour finir l'étape 3
1. Tester la migration sur une copie : `doctrine:migrations:migrate`, puis `migrate prev`, puis `migrate` à nouveau.
2. L'appliquer sur la base locale : `php bin/console doctrine:migrations:migrate`.
3. Tester les parcours : don lié à une urgence passé à « payé » → le montant collecté augmente ; modification d'une annonce → la date d'expiration ne change pas ; deux offres au même titre → slugs différents ; suppression d'une offre avec candidatures.
4. `php bin/console lint:twig templates` et `php bin/console doctrine:schema:validate`.

## Étapes suivantes proposées
- **Étape 4** : tout regrouper dans EasyAdmin et supprimer les pages admin du site public.
- **Étape 5** : accessibilité (menus au clavier, `aria-expanded`, WCAG 2.1 AA) et bugs front (CSS non chargé, Stimulus en double, Turbo et menu mobile).
- **Étape 6** : PHPUnit, PHPStan, puis les nouvelles fonctionnalités du §6.

## À faire de votre côté (production)
- Les **6 anciennes migrations** sont en SQL SQLite et **échoueront sur MySQL** : à régler avant le déploiement.
- Créer `var/uploads/` sur le serveur et l'inclure dans les **sauvegardes** (les CV y sont).
- Définir un vrai secret : `php bin/console secrets:set APP_SECRET --env=prod`.
- Configurer un vrai `MAILER_DSN` dans `.env.local` (sinon aucun e-mail ne part) et un expéditeur cohérent avec le SMTP (SPF/DMARC).
- `cache:clear` dépasse 128 Mo : `php -d memory_limit=-1 bin/console cache:clear`.
- Le Gmail personnel et l'ancien secret de dev restent dans l'**historique git** : à purger si le dépôt devient public.
- Vérifier l'image `certificat-daptitude-physique…jpg` dans `public/uploads/albums/` (document médical ?).
- Ne jamais charger `RelationMediaFixtures` en production.
- Supprimer `var/tmp-tests/` et les sauvegardes `var/data.db.bak-*` quand tout est validé.
