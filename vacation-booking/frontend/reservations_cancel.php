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

$reservationId = $_POST['reservation_id'] ?? null;
if (!$reservationId) {
    echo 'Reservation id missing';
    exit;
}

try {
    $res = $db->reservations->findOne(['_id' => new MongoDB\BSON\ObjectId($reservationId)]);
} catch (Exception $e) {
    app_log('Reservations cancel find error (frontend)', ['error' => $e->getMessage()]);
    echo 'Identifiant invalide';
    exit;
}

if (!$res) {
    echo 'Réservation introuvable';
    exit;
}

// verify owner or admin
if ((string)$res['user_id'] !== (string)$_SESSION['user']['_id'] && ($_SESSION['user']['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo 'Accès refusé';
    exit;
}

// mark as cancelled
try {
    $cancelResult = $db->reservations->updateOne(
        ['_id' => $res['_id'], 'status' => ['$ne' => 'annulée']],
        ['$set' => ['status' => 'annulée', 'cancelled_at' => date('c')]]
    );

    if (($cancelResult->getModifiedCount() ?? 0) < 1) {
        header('Location: booking_confirm.php?id=' . urlencode($reservationId) . '&cancelled=1');
        exit;
    }

    // Restore stock for the offer
    $offerObjectId = $res['offer_id'] ?? null;
    if ($offerObjectId) {
        // Backfill legacy offers (no quantity field yet)
        $db->offers->updateOne(
            ['_id' => $offerObjectId, 'quantity' => ['$exists' => false]],
            ['$set' => ['quantity' => 0]]
        );
        $db->offers->updateOne(
            ['_id' => $offerObjectId],
            ['$inc' => ['quantity' => 1], '$set' => ['available' => true]]
        );
    }
} catch (Exception $e) {
    app_log('Reservations cancel update error (frontend)', ['error' => $e->getMessage()]);
    echo 'Erreur lors de l\'annulation';
    exit;
}

header('Location: booking_confirm.php?id=' . urlencode($reservationId) . '&cancelled=1');
exit;
