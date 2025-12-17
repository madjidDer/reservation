<?php
require_once __DIR__ . '/../backend/config/session.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
// ensure MongoDB connection is available for the dashboard
include __DIR__ . '/../backend/config/mongo.php';
// compute next reservation early so we can show it beside the user's name
$nextReservation = null;
$nextOffer = null;
try {
    $userId = $_SESSION['user']['_id'];
    $userObjectId = new MongoDB\BSON\ObjectId($userId);
    $today = date('Y-m-d');
    $nextReservation = $db->reservations->findOne([
        'user_id' => $userObjectId,
        'date' => ['$gte' => $today]
    ], ['sort' => ['date' => 1]]);
    if ($nextReservation) {
        $nextOffer = $db->offers->findOne(['_id' => $nextReservation['offer_id']]);
    }
} catch (Exception $e) {
    $nextReservation = null;
}
?>
<?php
$title = 'Mon compte';
require __DIR__ . '/partials/header.php';

// Compute admin URL under subfolder installs
$docRoot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\'));
$scriptDir = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_FILENAME'] ?? '')));
$projectBase = '';
if ($docRoot !== '' && strpos($scriptDir, $docRoot) === 0) {
    $projectBase = substr($scriptDir, strlen($docRoot));
} else {
    $projectBase = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/\\');
}
if ($projectBase !== '' && $projectBase[0] !== '/') {
    $projectBase = '/' . $projectBase;
}
$adminUrl = $projectBase . '/backend/admin/dashboard.php';
?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h1 class="h3 mb-1">Bienvenue, <?php echo htmlspecialchars($user['name'] ?? $user['email']); ?></h1>
    <div class="vb-muted">Gérez vos réservations et explorez de nouvelles offres.</div>
  </div>
  <div class="d-flex gap-2 flex-wrap justify-content-end">
    <a href="offers.php" class="btn btn-primary">Parcourir les offres</a>
    <a href="reservations.php" class="btn btn-outline-secondary">Mes réservations</a>
    <?php if (($user['role'] ?? '') === 'admin'): ?>
      <a href="<?php echo htmlspecialchars($adminUrl); ?>" class="btn btn-warning">Admin</a>
    <?php endif; ?>
  </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                  <div class="card-body">
                    <div class="text-muted">Informations</div>
                    <div class="fw-semibold mt-2"><?php echo htmlspecialchars($user['email']); ?></div>
                    <div class="vb-muted">Rôle : <?php echo htmlspecialchars($user['role'] ?? 'client'); ?></div>
                  </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                  <div class="card-body">
                    <div class="text-muted">Prochaine réservation</div>
                    <?php if ($nextReservation): ?>
                      <div class="fw-semibold mt-2"><?php echo htmlspecialchars($nextOffer['title'] ?? 'Offre'); ?></div>
                      <div class="vb-muted">Date : <?php echo htmlspecialchars($nextReservation['date']); ?></div>
                      <div class="vb-muted">Statut : <?php echo htmlspecialchars($nextReservation['status']); ?></div>
                      <a href="booking_confirm.php?id=<?php echo (string)$nextReservation['_id']; ?>" class="btn btn-sm btn-outline-primary mt-3">Voir</a>
                    <?php else: ?>
                      <div class="vb-muted mt-2">Aucune réservation à venir.</div>
                    <?php endif; ?>
                  </div>
                </div>
            </div>
        </div>
    </div>
  
                <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h2 class="h5 mb-0">Offres disponibles</h2>
                            <a class="btn btn-sm btn-outline-secondary" href="offers.php">Tout voir</a>
                        </div>
            <div class="row">
            <?php
                // show a few available offers
                try {
                    $availableOffers = $db->offers->find(['available' => true], ['limit' => 3, 'sort' => ['_id' => -1]]);
                } catch (Exception $e) {
                    $availableOffers = [];
                }
                foreach ($availableOffers as $o):
            ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($o['photos'][0])): ?>
                            <img src="<?php echo htmlspecialchars($o['photos'][0]); ?>" class="card-img-top vb-card-img" alt="<?php echo htmlspecialchars($o['title']); ?>">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($o['title']); ?></h5>
                            <p class="card-text vb-muted"><?php echo htmlspecialchars(substr($o['description'] ?? '', 0, 80)); ?>...</p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                              <div class="fw-semibold"><?php echo htmlspecialchars($o['price']); ?> €</div>
                              <a href="offer.php?id=<?php echo (string)$o['_id']; ?>" class="btn btn-sm btn-primary">Voir</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
    </div>
    </div>

<?php require __DIR__ . '/partials/footer.php'; ?>
