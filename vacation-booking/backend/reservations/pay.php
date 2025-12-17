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

$reservationId = $_POST['reservation_id'] ?? null;
if (!$reservationId) {
    echo 'Reservation id missing';
    exit;
}

try {
    $res = $db->reservations->findOne(['_id' => new MongoDB\BSON\ObjectId($reservationId)]);
} catch (Exception $e) {
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
$db->reservations->updateOne(['_id' => $res['_id']], ['$set' => ['status' => 'payée', 'paid_at' => date('c')]]);

header('Location: ../../frontend/booking_confirm.php?id=' . urlencode($reservationId) . '&paid=1');
exit;
