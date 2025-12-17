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

$offerObjectId = null;
try {
    $offerObjectId = new MongoDB\BSON\ObjectId($offerId);
} catch (Exception $e) {
    echo 'Offre invalide';
    exit;
}

try {
    // Backfill legacy offers (no quantity field yet)
    $db->offers->updateOne(
        ['_id' => $offerObjectId, 'quantity' => ['$exists' => false], 'available' => true],
        ['$set' => ['quantity' => 10]]
    );
    $db->offers->updateOne(
        ['_id' => $offerObjectId, 'quantity' => ['$exists' => false], 'available' => ['$ne' => true]],
        ['$set' => ['quantity' => 0]]
    );

    // prevent duplicate reservations for same user & offer
    $existing = $db->reservations->findOne([
        'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user']['_id']),
        'offer_id' => $offerObjectId,
        'status' => ['$in' => ['confirmée', 'en attente', 'payée']]
    ]);
    if ($existing) {
        $insertedId = (string)$existing['_id'];
    } else {
        // Decrement stock atomically (prevents going below 0)
        $stockUpdate = $db->offers->updateOne(
            ['_id' => $offerObjectId, 'available' => true, 'quantity' => ['$gte' => 1]],
            ['$inc' => ['quantity' => -1]]
        );
        if (($stockUpdate->getModifiedCount() ?? 0) < 1) {
            header('Location: offer.php?id=' . urlencode($offerId) . '&error=' . urlencode('complet'));
            exit;
        }

        $res = $db->reservations->insertOne([
            'user_id' => new MongoDB\BSON\ObjectId($_SESSION['user']['_id']),
            'offer_id' => $offerObjectId,
            'date' => date('Y-m-d'),
            'status' => 'confirmée',
            'created_at' => date('c')
        ]);
        $insertedId = (string)$res->getInsertedId();

        // If stock reached 0, mark offer unavailable.
        $db->offers->updateOne(
            ['_id' => $offerObjectId, 'quantity' => ['$lte' => 0]],
            ['$set' => ['available' => false, 'quantity' => 0]]
        );
    }
} catch (Exception $e) {
    // If reservation insert fails after stock decrement, compensate best-effort.
    if (!empty($offerObjectId)) {
        try {
            $db->offers->updateOne(
                ['_id' => $offerObjectId],
                ['$inc' => ['quantity' => 1], '$set' => ['available' => true]]
            );
        } catch (Exception $e2) {
            // ignore
        }
    }
    app_log('Reservations create error (frontend)', ['error' => $e->getMessage()]);
    echo 'Erreur lors de la création de la réservation';
    exit;
}

header('Location: booking_confirm.php?id=' . urlencode($insertedId));
exit;
