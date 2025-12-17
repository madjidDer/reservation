<?php
$title = "Admin - Éditer l'offre";
require __DIR__ . '/partials/header.php';
include '../config/mongo.php';
require_once __DIR__ . '/../config/csrf.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Offre non spécifiée.";
    exit;
}

try {
    $offer = $db->offers->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
} catch (Exception $e) {
    echo "Identifiant invalide.";
    exit;
}

if (!$offer) {
    echo "Offre introuvable.";
    exit;
}

// Normalize photos field for safe display (MongoDB may return BSONArray)
$offerPhotos = $offer['photos'] ?? [];
if (is_string($offerPhotos)) {
  $offerPhotos = [$offerPhotos];
} elseif (is_iterable($offerPhotos) && !is_array($offerPhotos)) {
  $tmp = [];
  foreach ($offerPhotos as $p) {
    $tmp[] = $p;
  }
  $offerPhotos = $tmp;
} elseif (!is_array($offerPhotos)) {
  $offerPhotos = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    $error = 'Session expirée. Veuillez réessayer.';
  } else {
    $title = trim($_POST['title'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $photos_input = trim($_POST['photos'] ?? '');
    // Accept comma-separated URLs. Also accept ';' as separator.
    $photos_input = str_replace(';', ',', $photos_input);
    $photos = $photos_input === '' ? [] : array_values(array_filter(array_map('trim', explode(',', $photos_input)), static fn($v) => $v !== ''));

      if ($title === '' || $type === '') {
        $error = 'Le titre et le type sont requis.';
      } else {
        $db->offers->updateOne(['_id' => $offer['_id']], ['$set' => [
          'type' => $type,
          'title' => $title,
          'description' => $description,
          'price' => $price,
          'available' => isset($_POST['available']) ? true : false,
          'photos' => $photos
        ]]);
        header('Location: offers.php');
        exit;
      }
    }
}

?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="mb-0">Éditer l'offre</h1>
  <div>
    <button class="btn btn-primary" type="submit" form="edit-offer-form">Sauvegarder</button>
    <a class="btn btn-link" href="offers.php">Annuler</a>
  </div>
</div>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body p-4">
    <form id="edit-offer-form" method="post" action="edit_offer.php?id=<?php echo (string)$offer['_id']; ?>">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
  <div class="mb-3">
    <label class="form-label">Type</label>
    <input class="form-control" name="type" value="<?php echo htmlspecialchars($offer['type'] ?? ''); ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Titre</label>
    <input class="form-control" name="title" value="<?php echo htmlspecialchars($offer['title'] ?? ''); ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Description</label>
    <textarea class="form-control" name="description"><?php echo htmlspecialchars($offer['description'] ?? ''); ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Prix</label>
    <input class="form-control" name="price" type="number" step="0.01" value="<?php echo htmlspecialchars($offer['price'] ?? ''); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Photos (URLs séparées par des virgules)</label>
    <input class="form-control" name="photos" value="<?php echo htmlspecialchars(implode(', ', array_map('strval', $offerPhotos))); ?>">
  </div>
  <div class="mb-3 form-check">
    <input class="form-check-input" type="checkbox" name="available" id="available" <?php echo !empty($offer['available']) ? 'checked' : ''; ?>>
    <label class="form-check-label" for="available">Disponible</label>
  </div>
  <button class="btn btn-primary" type="submit">Sauvegarder</button>
  <a class="btn btn-link" href="offers.php">Annuler</a>
    </form>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
