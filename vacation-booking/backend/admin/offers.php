<?php
$title = 'Admin - Offres';
require __DIR__ . '/partials/header.php';
include '../config/mongo.php';
require_once __DIR__ . '/../config/csrf.php';

// Fetch offers
$offers = $db->offers->find();

// Stats: total offers
$totalOffers = $db->offers->countDocuments();

// Offers by type
$byType = $db->offers->aggregate([
    ['$group' => ['_id' => '$type', 'count' => ['$sum' => 1]]],
    ['$sort' => ['count' => -1]]
]);

// Top reserved offers
$topReserved = $db->reservations->aggregate([
    ['$group' => ['_id' => '$offer_id', 'count' => ['$sum' => 1]]],
    ['$sort' => ['count' => -1]],
    ['$limit' => 5]
]);

?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="mb-1">Admin</h1>
    <div class="text-muted">Gestion des offres & statistiques</div>
  </div>
  <div>
    <a class="btn btn-outline-secondary" href="dashboard.php">Dashboard</a>
    <a class="btn btn-success" href="add_offer.php">+ Ajouter une offre</a>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="text-muted">Total offres</div>
        <div class="display-6 mb-0"><?php echo (int)$totalOffers; ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="text-muted">Types d'offres</div>
        <div class="display-6 mb-0"><?php echo (int)$db->offers->countDocuments(['type' => ['$exists' => true]]); ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="text-muted">Top réservations</div>
        <div class="display-6 mb-0"><?php echo (int)$db->reservations->countDocuments(); ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-body">
        <div class="fw-semibold">Offres par type</div>
      </div>
      <div class="card-body p-0">
        <div class="list-group list-group-flush">
          <?php $hasTypes = false; foreach ($byType as $t): $hasTypes = true; ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div><?php echo htmlspecialchars($t['_id'] ?? 'Autre'); ?></div>
              <span class="badge text-bg-primary rounded-pill"><?php echo (int)$t['count']; ?></span>
            </div>
          <?php endforeach; ?>
          <?php if (!$hasTypes): ?>
            <div class="list-group-item text-muted">Aucune donnée.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-body">
        <div class="fw-semibold">Top offres réservées</div>
      </div>
      <div class="card-body p-0">
        <div class="list-group list-group-flush">
          <?php $hasTop = false; foreach ($topReserved as $r): $hasTop = true;
              $offer = $db->offers->findOne(['_id' => $r['_id']]);
          ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div class="me-3">
                <div class="fw-semibold"><?php echo htmlspecialchars($offer['title'] ?? 'Offre supprimée'); ?></div>
                <div class="text-muted" style="font-size:.9rem">ID: <?php echo htmlspecialchars((string)$r['_id']); ?></div>
              </div>
              <span class="badge text-bg-success rounded-pill"><?php echo (int)$r['count']; ?></span>
            </div>
          <?php endforeach; ?>
          <?php if (!$hasTop): ?>
            <div class="list-group-item text-muted">Aucune réservation pour le moment.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<h2 class="mt-4">Liste des offres</h2>
<table class="table table-striped" id="list">
  <thead>
    <tr><th>Title</th><th>Type</th><th>Price</th><th>Available</th><th>Actions</th></tr>
  </thead>
  <tbody>
  <?php foreach ($offers as $offer): ?>
    <tr>
      <td><?php echo htmlspecialchars($offer['title']); ?></td>
      <td><?php echo htmlspecialchars($offer['type'] ?? ''); ?></td>
      <td><?php echo htmlspecialchars($offer['price']); ?> €</td>
      <td><?php echo !empty($offer['available']) ? 'Oui' : 'Non'; ?></td>
      <td>
        <a class="btn btn-sm btn-primary" href="edit_offer.php?id=<?php echo (string)$offer['_id']; ?>">Éditer</a>
        <form style="display:inline-block" method="post" action="delete_offer.php" onsubmit="return confirm('Supprimer cette offre ?');">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <input type="hidden" name="id" value="<?php echo (string)$offer['_id']; ?>">
          <button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php require __DIR__ . '/partials/footer.php'; ?>
