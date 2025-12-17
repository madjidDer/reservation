
# Vacation Booking

Application de réservation d'expériences (PHP + MongoDB) avec espace client, espace admin, sécurité de base (CSRF, session partagée) et gestion de stock (quantité) par évènement.

## Fonctionnalités

- Parcours des offres + détail offre
- Inscription / Connexion / Déconnexion
- Réservation (paiement simulé)
- Annulation de réservation
- Admin : gestion des offres (CRUD)
- Stock : décrément à chaque réservation, incrément à chaque annulation, évènement indisponible à 0

## Stack

- PHP (procédural)
- MongoDB (via Composer : `mongodb/mongodb`)
- Bootstrap 5
- XAMPP/Apache recommandé (Windows)

## Prérequis

- XAMPP (Apache + PHP)
- MongoDB en local (par défaut : `mongodb://localhost:27017`)
- Composer (pour installer les dépendances)

## Installation

1) Placer le projet dans `C:\xampp\htdocs\vacation-booking`

2) Installer les dépendances :

```bash
cd C:\xampp\htdocs\vacation-booking
composer install
```

3) Vérifier la connexion MongoDB dans [backend/config/mongo.php](backend/config/mongo.php)

```php
$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->vacation_db;
```

4) (Recommandé) Créer l'index unique email :

```bash
php tools\create_indexes.php
```

5) Seed (admin + offres) :

```bash
php tools\seed.php
```

## Lancer l'application

### Option A — Apache (XAMPP) (recommandé)

- URL principale : `http://localhost/vacation-booking/`

### Option B — Serveur PHP intégré

```bash
php -S localhost:8000 -t C:\xampp\htdocs\vacation-booking\frontend
```

Puis : `http://localhost:8000/`

## URLs

- Accueil : `http://localhost/vacation-booking/`
- Offres : `http://localhost/vacation-booking/frontend/offers.php`
- Connexion : `http://localhost/vacation-booking/frontend/login.php`
- Inscription : `http://localhost/vacation-booking/frontend/register.php`
- Mes réservations : `http://localhost/vacation-booking/frontend/reservations.php`

### Admin

- Dashboard admin : `http://localhost/vacation-booking/backend/admin/dashboard.php`
- Gestion des offres : `http://localhost/vacation-booking/backend/admin/offers.php`

Identifiants admin (seed) :

- Email : `admin@vacation-booking.local`
- Mot de passe : `Admin1234`

## Stock (quantité)

- Champ `quantity` sur chaque offre (places disponibles)
- Réserver : `quantity -= 1` (refus si `quantity == 0`)
- Annuler : `quantity += 1`
- À `0` : `available=false` automatiquement

## Sécurité & maintenance (résumé)

- CSRF sur les formulaires et validation côté serveur
- Sessions centralisées : [backend/config/session.php](backend/config/session.php)
- Rate limiting (login/register) stocké dans `logs/ratelimit/`
- Logger applicatif : [backend/config/logger.php](backend/config/logger.php)

## Notes GitHub

- Le dossier `vendor/` et `logs/` ne doivent pas être commit.
- Utiliser `.gitignore` fourni à la racine du projet.
