
# Vacation Booking (PHP)

## Le lien “officiel” recommandé (XAMPP / Apache)

Si tu utilises XAMPP (Apache), ton projet est accessible via **un seul point d’entrée** :

- `http://localhost/vacation-booking/`

Ce lien redirige vers le frontend :

- `http://localhost/vacation-booking/frontend/index.php`

## Liens utiles (Apache)

- Accueil: `http://localhost/vacation-booking/`
- Offres: `http://localhost/vacation-booking/frontend/offers.php`
- Connexion: `http://localhost/vacation-booking/frontend/login.php`
- Inscription: `http://localhost/vacation-booking/frontend/register.php`
- Dashboard (après login): `http://localhost/vacation-booking/frontend/dashboard.php`

### Admin (Apache)

- Dashboard admin: `http://localhost/vacation-booking/backend/admin/dashboard.php`
- Gestion des offres admin: `http://localhost/vacation-booking/backend/admin/offers.php`

## Pourquoi `localhost` et `localhost:8000` affichent 2 pages différentes ?

- `http://localhost/` = Apache (port 80) de XAMPP (dans ton `htdocs`, il redirige vers `/dashboard/` par défaut)
- `http://localhost:8000/` = un **autre serveur** (souvent `php -S ...`) et donc **une autre racine**

## Si tu veux quand même utiliser `localhost:8000`

Lance le serveur PHP en pointant exactement sur le dossier `frontend` :

```bash
php -S localhost:8000 -t C:\xampp\htdocs\vacation-booking\frontend
```

Puis utilise :

- `http://localhost:8000/`

Recommandation: choisis **un seul** mode (Apache OU port 8000) pour éviter la confusion.

