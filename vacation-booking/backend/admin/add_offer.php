<?php
$title = 'Admin - Ajouter une offre';
require __DIR__ . '/partials/header.php';
include '../config/mongo.php';
require_once __DIR__ . '/../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    $error = 'Session expirée. Veuillez réessayer.';
  } else {
    // Simple validation
    $title = trim($_POST['title'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $photos_input = trim($_POST['photos'] ?? '');
    $photos = $photos_input === '' ? [] : array_map('trim', explode(',', $photos_input));

      if ($title === '' || $type === '') {
        $error = 'Le titre et le type sont requis.';
      } else {
        $db->offers->insertOne([
          'type' => $type,
          'title' => $title,
          'description' => $description,
          'price' => $price,
          'available' => isset($_POST['available']) ? true : false,
          'photos' => $photos
        ]);
        header('Location: offers.php');
        exit;
      }
    }
}

?>
<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h1 class="h3 mb-1">Ajouter une offre</h1>
    <div class="text-muted">Créez une nouvelle offre.</div>
  </div>
  <div>
    <a class="btn btn-outline-secondary" href="offers.php">Retour</a>
  </div>
</div>

<?php if (!empty($error)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body p-4">
    <form method="post" action="add_offer.php">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
  <div class="mb-3">
    <label class="form-label">Type</label>
    <input class="form-control" name="type" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Titre</label>
    <input class="form-control" name="title" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Description</label>
    <textarea class="form-control" name="description"></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Prix</label>
    <input class="form-control" name="price" type="number" step="0.01">
  </div>
  <div class="mb-3">
    <label class="form-label">Photos (URLs séparées par des virgules)</label>
    <input class="form-control" name="photos">
  </div>
  <div class="mb-3 form-check">
    <input class="form-check-input" type="checkbox" name="available" id="available">
    <label class="form-check-label" for="available">Disponible</label>
  </div>
  <div class="d-flex gap-2">
    <button class="btn btn-primary" type="submit">Ajouter</button>
    <a class="btn btn-outline-secondary" href="offers.php">Annuler</a>
  </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
