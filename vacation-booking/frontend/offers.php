<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../backend/config/mongo.php';

$q = trim($_GET['q'] ?? '');
$filter = [];
if ($q !== '') {
    // try text search, fallback to title regex
    $filter = [
        '$or' => [
            ['title' => new MongoDB\BSON\Regex($q, 'i')],
            ['description' => new MongoDB\BSON\Regex($q, 'i')]
        ]
    ];
}

$offers = $db->offers->find($filter);
?>
<?php
$title = 'Offres';
require __DIR__ . '/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h1 class="h3 mb-1">Offres</h1>
    <div class="vb-muted">Trouvez une expérience qui vous correspond.</div>
  </div>
</div>

<form class="mb-4" method="get" action="offers.php">
  <div class="input-group">
    <input type="search" name="q" value="<?php echo htmlspecialchars($q); ?>" class="form-control" placeholder="Rechercher une destination, activité...">
    <button class="btn btn-primary" type="submit">Rechercher</button>
  </div>
</form>

<div class="row">
<?php foreach ($offers as $offer): ?>
  <div class="col-md-4 mb-4">
    <div class="card h-100 shadow-sm">
      <?php if (!empty($offer['photos'][0])): ?>
        <img src="<?php echo htmlspecialchars($offer['photos'][0]); ?>" class="card-img-top vb-card-img" alt="<?php echo htmlspecialchars($offer['title']); ?>">
      <?php endif; ?>
      <div class="card-body d-flex flex-column">
        <h5 class="card-title"><?php echo htmlspecialchars($offer['title']); ?></h5>
        <p class="card-text vb-muted"><?php echo htmlspecialchars(substr($offer['description'], 0, 120)); ?>...</p>
        <div class="mt-auto d-flex justify-content-between align-items-center">
          <div class="fw-semibold"><?php echo htmlspecialchars($offer['price']); ?> €</div>
          <a href="offer.php?id=<?php echo (string)$offer['_id']; ?>" class="btn btn-primary">Voir & Réserver</a>
        </div>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
