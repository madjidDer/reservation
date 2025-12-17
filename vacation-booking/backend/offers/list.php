<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/mongo.php';

$offers = $db->offers->find([], ['sort' => ['_id' => -1]]);

?><!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/vacation-booking/frontend/assets/app.css">
    <title>Offres (backend)</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="/vacation-booking/">A2LMN Booking</a>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-light" href="/vacation-booking/frontend/offers.php">Voir côté site</a>
            <a class="btn btn-secondary" href="/vacation-booking/backend/admin/dashboard.php">Admin</a>
        </div>
    </div>
</nav>

<main class="container py-4">
    <div class="vb-hero p-4 p-md-5 mb-4 shadow-sm">
        <h1 class="h3 fw-bold mb-2">Offres (route backend)</h1>
        <div class="vb-muted">Cette page servait du HTML brut. Elle est maintenant stylée avec le thème global.</div>
    </div>

    <div class="row">
        <?php foreach ($offers as $offer): ?>
            <?php
                $title = (string)($offer['title'] ?? '');
                $description = (string)($offer['description'] ?? '');
                $price = $offer['price'] ?? '';
                $photo = '';
                if (!empty($offer['photos'][0]) && is_string($offer['photos'][0])) {
                    $photo = $offer['photos'][0];
                }
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if ($photo !== ''): ?>
                        <img src="<?php echo htmlspecialchars($photo); ?>" class="card-img-top vb-card-img" alt="<?php echo htmlspecialchars($title); ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h2 class="h5 mb-2"><?php echo htmlspecialchars($title); ?></h2>
                        <p class="vb-muted mb-3"><?php echo htmlspecialchars(mb_strimwidth($description, 0, 140, '…')); ?></p>
                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <div class="fw-semibold"><?php echo htmlspecialchars((string)$price); ?> €</div>
                            <a class="btn btn-sm btn-primary" href="/vacation-booking/frontend/offers.php">Ouvrir sur le site</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
