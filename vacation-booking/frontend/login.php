<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<?php
$title = 'Connexion';
require __DIR__ . '/partials/header.php';
require_once __DIR__ . '/../backend/config/csrf.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h1 class="h3 mb-3">Connexion</h1>
        <form method="post" action="login_submit.php">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
<?php if (!empty($_GET['error'])): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input class="form-control" type="password" name="password" required>
          </div>
          <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">Se connecter</button>
            <a href="register.php" class="btn btn-outline-secondary">Créer un compte</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
