<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<?php
$title = 'Inscription';
require __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../backend/config/csrf.php';
?>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h3 mb-3">Inscription</h1>
		<form method="post" action="register_submit.php">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
		<div class="mb-3">
			<label class="form-label">Nom</label>
			<input class="form-control" type="text" name="name" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Email</label>
			<input class="form-control" type="email" name="email" required>
		</div>
		<div class="mb-3">
			<label class="form-label">Mot de passe</label>
			<input class="form-control" type="password" name="password" required>
		</div>
<?php if (!empty($_GET['error'])): ?>
  <div class="alert alert-danger">
    <?php
      $e = $_GET['error'];
      if ($e === 'missing') echo 'Veuillez remplir tous les champs.';
      elseif ($e === 'invalidemail') echo 'Adresse email invalide.';
      elseif ($e === 'exists') echo 'Un compte avec cet email existe déjà.';
			elseif ($e === 'csrf') echo 'Session expirée. Veuillez réessayer.';
			elseif ($e === 'ratelimit') echo 'Trop de tentatives. Veuillez réessayer plus tard.';
      else echo htmlspecialchars($e);
    ?>
  </div>
<?php elseif (!empty($_GET['registered'])): ?>
  <div class="alert alert-success">Inscription réussie. Vous pouvez vous connecter.</div>
<?php endif; ?>
		<div class="d-grid gap-2">
			<button class="btn btn-success" type="submit">Créer mon compte</button>
			<a href="login.php" class="btn btn-outline-secondary">J'ai déjà un compte</a>
		</div>
		</form>
	</div>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
