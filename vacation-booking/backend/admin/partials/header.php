<?php
// Admin shared layout header
require_once __DIR__ . '/../../config/session.php';

// Protection admin
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo "Accès refusé. Connectez-vous en tant qu'administrateur.";
    exit;
}

$title = $title ?? 'Admin';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../frontend/assets/app.css">
  <title><?php echo htmlspecialchars($title); ?></title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="dashboard.php">Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="offers.php">Gérer les offres</a></li>
      </ul>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-light" href="../../frontend/dashboard.php">Retour au site</a>
        <a class="btn btn-secondary" href="../../frontend/logout.php">Déconnexion</a>
      </div>
    </div>
  </div>
</nav>

<main class="container py-4">
