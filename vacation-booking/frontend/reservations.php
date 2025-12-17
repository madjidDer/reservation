<?php
require_once __DIR__ . '/../backend/config/session.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
include __DIR__ . '/../backend/config/mongo.php';

$userId = $_SESSION['user']['_id'];
// user_id stored as ObjectId in reservations, convert session id string to ObjectId for query
try {
  $userObjectId = new MongoDB\BSON\ObjectId($userId);
  $reservations = $db->reservations->find(['user_id' => $userObjectId]);
} catch (Exception $e) {
  // fallback: try string match (in case stored differently)
  $reservations = $db->reservations->find(['user_id' => $userId]);
}
?>
<?php
$title = 'Mes réservations';
require __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../backend/config/csrf.php';
?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h1 class="h3 mb-1">Mes réservations</h1>
    <div class="vb-muted">Suivez vos réservations et annulez si besoin.</div>
  </div>
  <div>
    <a href="dashboard.php" class="btn btn-outline-secondary">Retour au dashboard</a>
  </div>
</div>

<div class="mt-3">
<?php foreach ($reservations as $r):
    $offer = $db->offers->findOne(['_id' => $r['offer_id']]);
    $status = (string)($r['status'] ?? '');
    $badgeClass = 'text-bg-secondary';
    if ($status === 'payée' || $status === 'confirmée') $badgeClass = 'text-bg-success';
    elseif ($status === 'en attente') $badgeClass = 'text-bg-warning';
    elseif ($status === 'annulée') $badgeClass = 'text-bg-danger';
?>
  <div class="card mb-3 shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start gap-3">
        <div>
          <h5 class="card-title mb-1"><?php echo htmlspecialchars($offer['title'] ?? 'Offre supprimée'); ?></h5>
          <div class="vb-muted">Date : <?php echo htmlspecialchars($r['date']); ?></div>
        </div>
        <span class="badge <?php echo $badgeClass; ?> rounded-pill align-self-start"><?php echo htmlspecialchars($status); ?></span>
      </div>
      <?php if ($r['status'] !== 'annulée'): ?>
        <form class="mt-3" method="post" action="reservations_cancel.php" onsubmit="return confirm('Voulez-vous vraiment annuler cette réservation ?');">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars((string)$r['_id']); ?>">
          <button class="btn btn-sm btn-danger">Annuler</button>
        </form>
      <?php else: ?>
        <div class="text-muted mt-2">Annulée le <?php echo htmlspecialchars($r['cancelled_at'] ?? ''); ?></div>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
