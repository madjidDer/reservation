<?php
// Lightweight registration endpoint inside frontend to avoid absolute path issues.
require_once __DIR__ . '/../backend/config/session.php';
require_once __DIR__ . '/../backend/config/csrf.php';
require_once __DIR__ . '/../backend/config/rate_limit.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    header('Location: register.php?error=csrf');
    exit;
}

if (!rate_limit_check(rate_limit_key('register'), 5, 600)) {
    header('Location: register.php?error=ratelimit');
    exit;
}

require __DIR__ . '/../backend/config/mongo.php';

$name = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if ($name === '' || $email === '' || $password === '') {
    header('Location: register.php?error=missing');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: register.php?error=invalidemail');
    exit;
}

// check existing
$existing = $db->users->findOne(['email' => $email]);
if ($existing) {
    header('Location: register.php?error=exists');
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
try {
    $db->users->insertOne([
        'name' => $name,
        'email' => $email,
        'password' => $hash,
        'role' => 'client',
        'created_at' => new MongoDB\BSON\UTCDateTime((int)(microtime(true) * 1000))
    ]);
} catch (Exception $e) {
    app_log('Register insert error (frontend)', ['error' => $e->getMessage()]);
    if (strpos($e->getMessage(), 'E11000') !== false) {
        header('Location: register.php?error=exists');
    } else {
        header('Location: register.php?error=server');
    }
    exit;
}

header('Location: register.php?registered=1');
exit;
