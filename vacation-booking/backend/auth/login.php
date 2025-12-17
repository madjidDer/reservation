<?php
require_once __DIR__ . '/../config/session.php';
include '../config/mongo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: ../../frontend/login.php?error=' . urlencode('Veuillez fournir email et mot de passe'));
    exit;
}

$user = $db->users->findOne(['email' => $email]);
try {
    $user = $db->users->findOne(['email' => $email]);
} catch (Exception $e) {
    $logDir = __DIR__ . '/../../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    error_log("[".date('c')."] Login find error: " . $e->getMessage() . "\n", 3, $logDir . '/app.log');
    header('Location: ../../frontend/login.php?error=' . urlencode('Erreur serveur'));
    exit;
}

if ($user && password_verify($password, $user['password'])) {
    // store minimal user data in session to avoid serializing MongoDB objects that may cause issues
    $_SESSION['user'] = [
        '_id' => (string)$user['_id'],
        'name' => $user['name'] ?? null,
        'email' => $user['email'],
        'role' => $user['role'] ?? 'client'
    ];
    header("Location: ../../frontend/dashboard.php");
    exit;
} else {
    header('Location: ../../frontend/login.php?error=' . urlencode('Email ou mot de passe incorrect'));
    exit;
}
