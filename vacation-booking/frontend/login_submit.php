<?php
require_once __DIR__ . '/../backend/config/session.php';
require_once __DIR__ . '/../backend/config/csrf.php';
require_once __DIR__ . '/../backend/config/rate_limit.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    header('Location: login.php?error=' . urlencode('Session expirée. Veuillez réessayer.'));
    exit;
}

if (!rate_limit_check(rate_limit_key('login'), 10, 60)) {
    header('Location: login.php?error=' . urlencode('Trop de tentatives. Réessayez dans une minute.'));
    exit;
}

require __DIR__ . '/../backend/config/mongo.php';

$emailInput = trim($_POST['email'] ?? '');
$email = strtolower($emailInput);
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: login.php?error=' . urlencode('Veuillez fournir email et mot de passe'));
    exit;
}

try {
    $user = $db->users->findOne(['email' => $email]);
    if (!$user && $emailInput !== '' && $emailInput !== $email) {
        $user = $db->users->findOne(['email' => $emailInput]);
    }
} catch (Exception $e) {
    app_log('Login find error (frontend)', ['error' => $e->getMessage()]);
    header('Location: login.php?error=' . urlencode('Erreur serveur'));
    exit;
}

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = [
        '_id' => (string)$user['_id'],
        'name' => $user['name'] ?? null,
        'email' => strtolower((string)($user['email'] ?? $email)),
        'role' => $user['role'] ?? 'client'
    ];
    header('Location: dashboard.php');
    exit;
} else {
    header('Location: login.php?error=' . urlencode('Email ou mot de passe incorrect'));
    exit;
}
