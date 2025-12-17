<?php
require_once __DIR__ . '/../backend/config/session.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['id'])) {
    echo 'Réservation non spécifiée';
    exit;
}

$id = $_GET['id'];
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

$title = 'Paiement';
require __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../backend/config/csrf.php';

// verify owner
if (!isset($_SESSION['user']) || (string)$_SESSION['user']['_id'] !== (string)$reservation['user_id']) {
    echo 'Accès refusé';
    exit;
}

$offer = $db->offers->findOne(['_id' => $reservation['offer_id']]);

?>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h3 mb-2">Paiement</h1>
        <div class="vb-muted mb-3">Paiement simulé pour finaliser votre réservation.</div>
        <div class="alert alert-light border small mb-3">Réservation : <?php echo htmlspecialchars((string)$reservation['_id']); ?></div>

        <form method="post" action="pay_submit.php">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <input type="hidden" name="reservation_id" value="<?php echo htmlspecialchars((string)$reservation['_id']); ?>">
          <div class="d-grid gap-2">
            <button class="btn btn-success">Payer</button>
            <a class="btn btn-outline-secondary" href="reservations.php">Retour</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
