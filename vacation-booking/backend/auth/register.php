<?php
include '../config/mongo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($name === '' || $email === '' || $password === '') {
    header('Location: ../../frontend/register.php?error=missing');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../frontend/register.php?error=invalidemail');
    exit;
}

// check existing
$existing = $db->users->findOne(['email' => $email]);
if ($existing) {
    header('Location: ../../frontend/register.php?error=exists');
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
try {
    $db->users->insertOne([
        'name' => $name,
        'email' => $email,
        'password' => $hash,
        'role' => 'client',
        'created_at' => date('Y-m-d')
    ]);
} catch (Exception $e) {
    $logDir = __DIR__ . '/../../logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    error_log("[".date('c')."] Register insert error: " . $e->getMessage() . "\n", 3, $logDir . '/app.log');
    header('Location: ../../frontend/register.php?error=server');
    exit;
}

header('Location: ../../frontend/register.php?registered=1');
exit;
