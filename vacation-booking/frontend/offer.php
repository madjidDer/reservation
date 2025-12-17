<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../backend/config/session.php';
include __DIR__ . '/../backend/config/mongo.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Offre non spécifiée";
    exit;
}

try {
    $offer = $db->offers->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
} catch (Exception $e) {
    echo "Identifiant invalide";
    exit;
}

if (!$offer) {
    echo "Offre introuvable";
    exit;
}
?>
<?php
$title = (string)($offer['title'] ?? 'Offre');
require __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../backend/config/csrf.php';
?>

<a href="offers.php" class="btn btn-outline-secondary btn-sm mb-3">← Retour aux offres</a>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <?php if (!empty($offer['photos'][0])): ?>
        <img src="<?php echo htmlspecialchars($offer['photos'][0]); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="max-height:420px; width:100%; object-fit:cover;">
      <?php endif; ?>
      <div class="card-body">
        <h1 class="h3 mb-2"><?php echo htmlspecialchars($offer['title']); ?></h1>
        <div class="vb-muted mb-3">À partir de <span class="fw-semibold"><?php echo htmlspecialchars($offer['price']); ?> €</span></div>
        <div><?php echo nl2br(htmlspecialchars($offer['description'])); ?></div>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted">Réservation</div>
        <div class="fw-semibold mb-3">Finalisez en 1 clic</div>

<?php if (isset($_SESSION['user'])): ?>
        <form method="post" action="reservations_create.php">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <input type="hidden" name="offer_id" value="<?php echo (string)$offer['_id']; ?>">
          <div class="d-grid">
            <button class="btn btn-success" type="submit">Réserver (paiement simulé)</button>
          </div>
        </form>
<?php else: ?>
        <div class="alert alert-warning mb-0">Vous devez <a href="login.php">vous connecter</a> pour réserver.</div>
<?php endif; ?>

      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
