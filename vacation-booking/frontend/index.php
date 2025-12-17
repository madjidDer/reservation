<?php
$title = 'Accueil';
require __DIR__ . '/partials/header.php';
?>

<div class="vb-hero rounded-3 p-4 p-md-5 mb-4 shadow-sm">
	<div class="row align-items-center">
		<div class="col-lg-8">
			<h1 class="fw-bold mb-2">Réservez vos expériences en quelques clics</h1>
			<p class="vb-muted mb-0">Parcourez les offres, réservez, et gérez vos voyages depuis votre espace.</p>
		</div>
		<div class="col-lg-4 mt-3 mt-lg-0 d-flex gap-2 justify-content-lg-end">
			<a href="offers.php" class="btn btn-primary">Voir les offres</a>
			<?php if (!isset($_SESSION['user'])): ?>
				<a href="login.php" class="btn btn-outline-secondary">Connexion</a>
			<?php else: ?>
				<a href="dashboard.php" class="btn btn-outline-secondary">Mon compte</a>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php
// show some available offers on the homepage
include __DIR__ . '/../backend/config/mongo.php';
try {
		$homeOffers = $db->offers->find(['available' => true], ['limit' => 4, 'sort' => ['_id' => -1]]);
} catch (Exception $e) {
		$homeOffers = [];
}
?>

<?php if (!empty($homeOffers)): ?>
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2 class="h4 mb-0">Offres disponibles</h2>
		<a class="btn btn-sm btn-outline-secondary" href="offers.php">Tout voir</a>
	</div>
	<div class="row">
	<?php foreach ($homeOffers as $offer): ?>
		<div class="col-md-3 mb-3">
			<div class="card h-100 shadow-sm">
				<?php if (!empty($offer['photos'][0])): ?>
					<img src="<?php echo htmlspecialchars($offer['photos'][0]); ?>" class="card-img-top vb-card-img" alt="<?php echo htmlspecialchars($offer['title']); ?>">
				<?php endif; ?>
				<div class="card-body d-flex flex-column">
					<h5 class="card-title mb-1"><?php echo htmlspecialchars($offer['title']); ?></h5>
					<p class="card-text vb-muted"><?php echo htmlspecialchars(substr($offer['description'] ?? '', 0, 70)); ?>...</p>
					<div class="mt-auto d-flex justify-content-between align-items-center">
						<div class="fw-semibold"><?php echo htmlspecialchars($offer['price']); ?> €</div>
						<a href="offer.php?id=<?php echo (string)$offer['_id']; ?>" class="btn btn-sm btn-primary">Voir</a>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
<?php else: ?>
	<div class="alert alert-light border">Aucune offre disponible pour le moment.</div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
