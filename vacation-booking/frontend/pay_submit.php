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
    app_log('Pay find error (frontend)', ['error' => $e->getMessage()]);
    echo 'Identifiant invalide';
    exit;
}

if (!$res) {
    echo 'Réservation introuvable';
    exit;
}

// verify owner
if ((string)$res['user_id'] !== (string)$_SESSION['user']['_id']) {
    http_response_code(403);
    echo 'Accès refusé';
    exit;
}

// simulate payment: mark as payée and set paid_at
try {
    $db->reservations->updateOne(['_id' => $res['_id']], ['$set' => ['status' => 'payée', 'paid_at' => date('c')]]);
} catch (Exception $e) {
    app_log('Pay update error (frontend)', ['error' => $e->getMessage()]);
    echo 'Erreur lors du paiement';
    exit;
}

header('Location: booking_confirm.php?id=' . urlencode($reservationId) . '&paid=1');
exit;
