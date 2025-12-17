<?php
require_once __DIR__ . '/../backend/config/session.php';
require_once __DIR__ . '/../backend/config/csrf.php';
require_once __DIR__ . '/../backend/config/logger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    echo 'Session expirée. Veuillez réessayer.';
    exit;
}

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../backend/config/mongo.php';

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
    app_log('Reservations create error (frontend)', ['error' => $e->getMessage()]);
    echo 'Erreur lors de la création de la réservation';
    exit;
}

header('Location: booking_confirm.php?id=' . urlencode($insertedId));
exit;
