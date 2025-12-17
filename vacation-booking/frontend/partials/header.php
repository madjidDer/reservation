<?php
// Frontend shared layout header
require_once __DIR__ . '/../../backend/config/session.php';

$title = $title ?? 'Vacation Booking';
$user = $_SESSION['user'] ?? null;
$isLoggedIn = $user !== null;
$isAdmin = ($user['role'] ?? '') === 'admin';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/app.css">
  <title><?php echo htmlspecialchars($title); ?></title>
</head>
<body>
<div class="vb-page">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="index.php">Vacation Booking</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#vbNav" aria-controls="vbNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="vbNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="offers.php">Offres</a></li>
        <?php if ($isLoggedIn): ?>
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Mon compte</a></li>
          <li class="nav-item"><a class="nav-link" href="reservations.php">Mes réservations</a></li>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
          <li class="nav-item"><a class="nav-link" href="../backend/admin/dashboard.php">Admin</a></li>
        <?php endif; ?>
      </ul>

      <div class="d-flex gap-2">
        <?php if ($isLoggedIn): ?>
          <span class="navbar-text text-white-50 d-none d-lg-inline"><?php echo htmlspecialchars($user['email'] ?? ''); ?></span>
          <a class="btn btn-outline-light" href="logout.php">Déconnexion</a>
        <?php else: ?>
          <a class="btn btn-outline-light" href="login.php">Connexion</a>
          <a class="btn btn-success" href="register.php">Inscription</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<main class="container py-4">
