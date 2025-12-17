<?php
require_once __DIR__ . '/../backend/config/session.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo 'Réservation non spécifiée';
    exit;
}

include __DIR__ . '/../backend/config/mongo.php';

try {
    $reservation = $db->reservations->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
} catch (Exception $e) {
    echo 'Identifiant invalide';
    exit;
}

if (!$reservation) {
    echo 'Réservation introuvable';
    exit;
}

$offer = null;
try {
    $offer = $db->offers->findOne(['_id' => $reservation['offer_id']]);
} catch (Exception $e) {
    $offer = null;
}

$title = 'Confirmation';
require __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../backend/config/csrf.php';

$status = (string)($reservation['status'] ?? '');
$badgeClass = 'text-bg-secondary';
if ($status === 'payée' || $status === 'confirmée') $badgeClass = 'text-bg-success';
elseif ($status === 'en attente') $badgeClass = 'text-bg-warning';
elseif ($status === 'annulée') $badgeClass = 'text-bg-danger';
?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h1 class="h3 mb-1">Confirmation</h1>
    <div class="vb-muted">Votre réservation est enregistrée.</div>
  </div>
  <div>
    <a href="dashboard.php" class="btn btn-outline-secondary">Retour au dashboard</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-start gap-3">
      <div>
        <div class="text-muted">Offre</div>
        <div class="fw-semibold"><?php echo htmlspecialchars($offer['title'] ?? 'Offre'); ?></div>
        <div class="vb-muted">Date : <?php echo htmlspecialchars($reservation['date'] ?? ''); ?></div>
      </div>
      <span class="badge <?php echo $badgeClass; ?> rounded-pill"><?php echo htmlspecialchars($status); ?></span>
    </div>

    <hr>

    <div class="d-flex gap-2 flex-wrap">
      <?php if ($status !== 'payée' && $status !== 'annulée'): ?>
        <a href="payment.php?id=<?php echo urlencode((string)$reservation['_id']); ?>" class="btn btn-primary">Payer maintenant (simulation)</a>
      <?php endif; ?>

      <?php if ($status !== 'annulée'): ?>
        <form method="post" action="reservations_cancel.php" onsubmit="return confirm('Voulez-vous vraiment annuler cette réservation ?');">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars((string)$reservation['_id']); ?>">
          <button class="btn btn-danger" type="submit">Annuler</button>
        </form>
      <?php endif; ?>

      <a href="reservations.php" class="btn btn-outline-secondary">Voir mes réservations</a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
