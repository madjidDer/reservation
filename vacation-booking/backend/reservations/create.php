<?php
require_once __DIR__ . '/../config/session.php';
include '../config/mongo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: ../../frontend/login.php');
    exit;
}

$offerId = $_POST['offer_id'] ?? null;
if (!$offerId) {
    echo 'Offre invalide';
    exit;
}

try {
    // prevent duplicate reservations for same user & offer
    $existing = $db->reservations->findOne([
        'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user']['_id']),
        'offer_id' => new MongoDB\BSON\ObjectId($offerId),
        'status' => ['$in' => ['confirmée', 'en attente', 'payée']]
    ]);
    if ($existing) {
        $insertedId = (string)$existing['_id'];
    } else {
        $res = $db->reservations->insertOne([
            'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user']['_id']),
            'offer_id' => new MongoDB\BSON\ObjectId($offerId),
            'date' => date('Y-m-d'),
            'status' => 'confirmée',
            'created_at' => date('c')
        ]);
        $insertedId = (string)$res->getInsertedId();
    }
} catch (Exception $e) {
    $logDir = __DIR__ . '/../../logs';
    if (!is_dir($logDir)) { @mkdir($logDir, 0755, true); }
    error_log("[".date('c')."] Reservations create error (backend): " . $e->getMessage() . "\n", 3, $logDir . '/app.log');
    echo 'Erreur lors de la création de la réservation';
    exit;
}
// Redirect to frontend confirmation page
header('Location: ../../frontend/booking_confirm.php?id=' . urlencode($insertedId));
exit;
