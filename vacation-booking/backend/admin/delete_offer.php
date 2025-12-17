<?php
require_once __DIR__ . '/../config/session.php';
// Admin protection
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo "Accès refusé. Connectez-vous en tant qu'administrateur.";
    exit;
}

include '../config/mongo.php';
require_once __DIR__ . '/../config/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: offers.php');
    exit;
}

if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    header('Location: offers.php');
    exit;
}

$id = $_POST['id'] ?? null;
if ($id) {
    try {
        $db->offers->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
    } catch (Exception $e) {
        // ignore or log
    }
}

header('Location: offers.php');
exit;
