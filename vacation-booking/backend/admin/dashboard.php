<?php
$title = 'Admin - Dashboard';
require __DIR__ . '/partials/header.php';
include '../config/mongo.php';

$usersCount = (int)$db->users->countDocuments();
$reservationsCount = (int)$db->reservations->countDocuments();
$offersCount = (int)$db->offers->countDocuments();

?>

<div class="d-flex justify-content-between align-items-end mb-4">
	<div>
		<h1 class="h3 mb-1">Dashboard</h1>
		<div class="text-muted">Vue d'ensemble</div>
	</div>
</div>

<div class="row g-3">
	<div class="col-md-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<div class="text-muted">Utilisateurs</div>
				<div class="display-6 mb-0"><?php echo $usersCount; ?></div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<div class="text-muted">Réservations</div>
				<div class="display-6 mb-0"><?php echo $reservationsCount; ?></div>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="card border-0 shadow-sm h-100">
			<div class="card-body">
				<div class="text-muted">Offres</div>
				<div class="display-6 mb-0"><?php echo $offersCount; ?></div>
			</div>
		</div>
	</div>
</div>

<div class="row g-3 mt-1">
	<div class="col-lg-8">
		<div class="card border-0 shadow-sm">
			<div class="card-header bg-body fw-semibold">Actions rapides</div>
			<div class="card-body">
				<div class="d-flex flex-wrap gap-2">
					<a class="btn btn-primary" href="offers.php">Gérer les offres</a>
				</div>
				<div class="text-muted mt-3" style="font-size:.95rem">
					Astuce : la page <strong>Offres & stats</strong> contient les statistiques détaillées.
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="card border-0 shadow-sm">
			<div class="card-header bg-body fw-semibold">Accès</div>
			<div class="card-body">
				<div class="text-muted" style="font-size:.95rem">Connecté en tant que :</div>
				<div class="fw-semibold"><?php echo htmlspecialchars($_SESSION['user']['email'] ?? 'admin'); ?></div>
				<div class="text-muted" style="font-size:.9rem">Rôle : <?php echo htmlspecialchars($_SESSION['user']['role'] ?? 'admin'); ?></div>
			</div>
		</div>
	</div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
