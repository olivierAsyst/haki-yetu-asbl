# Site web HAKI YETU asbl

Site Symfony 7.4 / PHP 8.3 pour HAKI YETU asbl (Uvira, RD Congo), avec back-office de
gestion de contenu et upload d'images via VichUploaderBundle.

> ⚠️ **Important** : ce projet a été généré dans un environnement sans accès à Packagist
> (le dépôt des paquets Composer), il n'a donc **pas pu être installé/testé ici** avec
> `composer install`. Tous les fichiers (entités, contrôleurs, templates, formulaires,
> config, back-office) sont complets et prêts à l'emploi, mais vous devez lancer
> l'installation sur votre propre machine (ou serveur) en suivant les étapes ci-dessous.

## Fonctionnalités

- **Accueil** : hero, bandeau d'annonces (programme / activité / réunion / autre),
  statistiques d'impact, secteurs d'intervention, aperçu activités + galerie, partenaires.
- **Activités** : liste des activités (titre obligatoire, image/description/date facultatifs).
- **Galerie** : albums photo (titre obligatoire, description/date facultatifs), chaque
  album contient plusieurs photos avec légende facultative.
- **À propos** : contenu repris du dépliant (historique, vision, mission, valeurs,
  structure organisationnelle, partenaires).
- **Contact** : formulaire (nom, e-mail, sujet, message) qui enregistre le message en
  base **et** envoie un e-mail à `hakiyetuass@yahoo.fr` (adresse du dépliant, configurable
  dans `.env`).
- **Back-office** (`/admin`, protégé par login) : gestion complète des annonces,
  activités, albums, photos et consultation des messages reçus, via EasyAdminBundle.
- Upload d'images géré par **VichUploaderBundle** (renommage automatique, séparation des
  dossiers `uploads/activites` et `uploads/albums`).
- Thème **Tailwind CSS v4** installé via Webpack Encore, en suivant le guide officiel
  https://tailwindcss.com/docs/installation/framework-guides/symfony, entièrement responsive.

## Prérequis

- PHP **8.3** avec les extensions `ctype`, `iconv`, `pdo_mysql` (ou `pdo_pgsql`/`pdo_sqlite`)
- Composer 2
- Node.js **18+** et npm
- Un serveur MySQL 8 (ou adaptez `DATABASE_URL` pour PostgreSQL/SQLite)
- Le [Symfony CLI](https://symfony.com/download) (recommandé mais optionnel)

## Installation

```bash
# 1. Installer les dépendances PHP
composer install

# 2. Installer les dépendances JS et construire les assets Tailwind
npm install
npm run build        # ou "npm run watch" pendant le développement

# 3. Configurer l'environnement
cp .env .env.local
# → éditez .env.local : DATABASE_URL, MAILER_DSN, CONTACT_RECIPIENT_EMAIL, APP_SECRET

# 4. Créer la base de données et les tables
php bin/console doctrine:database:create
php bin/console make:migration        # génère la migration à partir des entités
php bin/console doctrine:migrations:migrate

# 5. Créer un premier compte administrateur pour le back-office
php bin/console app:create-admin

# 6. Lancer le serveur de développement
symfony server:start
# ou : php -S 127.0.0.1:8000 -t public
```

Le site est disponible sur `http://localhost:8000`, le back-office sur
`http://localhost:8000/admin`.

## Configurer l'envoi d'e-mails (formulaire de contact)

Le `MAILER_DSN` par défaut (`smtp://localhost`) ne fonctionnera pas tel quel. Options :

- **Yahoo** (adresse du dépliant) : créez un mot de passe d'application dans les
  paramètres de sécurité Yahoo, puis :
  ```
  MAILER_DSN=smtp://hakiyetuass%40yahoo.fr:MOT_DE_PASSE_APPLICATION@smtp.mail.yahoo.com:465?encryption=ssl
  ```
- **Gmail**, **SendGrid**, **Mailjet**, etc. : Symfony Mailer supporte ces transports
  nativement (`composer require symfony/sendgrid-mailer`, etc.) — voir la
  [documentation Symfony Mailer](https://symfony.com/doc/current/mailer.html).

Le destinataire des messages du formulaire est défini par `CONTACT_RECIPIENT_EMAIL` dans
`.env` (déjà réglé sur `hakiyetuass@yahoo.fr`).

## Structure du back-office

| Menu               | Ce qu'on y gère                                            |
|---------------------|-------------------------------------------------------------|
| Annonces            | Bandeau affiché en haut de l'accueil (programme, réunion...) |
| Activités           | Fiches activités (titre obligatoire, image/description/date facultatifs) |
| Albums              | Créez d'abord l'album (titre obligatoire)                   |
| Photos              | Ajoutez des photos à un album existant, avec légende         |
| Messages reçus       | Consultez les messages envoyés via le formulaire de contact  |
| Comptes admin        | Créez d'autres comptes pour l'équipe                         |

## Notes techniques

- Les entités utilisent des attributs PHP 8 (pas d'annotations Doctrine XML/YAML).
- Les uploads sont stockés dans `public/uploads/activites` et `public/uploads/albums`
  (créés vides avec `.gitkeep`) — pensez à sauvegarder ce dossier en production.
- Le design (police Fraunces/Work Sans, palette bleu nuit / terracotta) est défini dans
  `assets/styles/app.css` via `@theme` (configuration Tailwind v4 "CSS-first").
- Le back-office et le site public partagent le même firewall Symfony, mais avec un
  pare-feu dédié (`admin`) et un contrôle d'accès `ROLE_ADMIN` sur `/admin/*`.
