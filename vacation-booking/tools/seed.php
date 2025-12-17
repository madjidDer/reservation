<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/config/mongo.php';

// Minimal seed: admin user + a few offers

$adminEmail = 'admin@vacation-booking.local';
$existingAdmin = $db->users->findOne(['email' => $adminEmail]);
if (!$existingAdmin) {
    $db->users->insertOne([
        'name' => 'Admin',
        'email' => $adminEmail,
        'password' => password_hash('Admin1234', PASSWORD_DEFAULT),
        'role' => 'admin',
        'created_at' => new MongoDB\BSON\UTCDateTime((int)(microtime(true) * 1000))
    ]);
    echo "Seed: admin created (admin@vacation-booking.local / Admin1234)\n";
}

$offersCount = $db->offers->countDocuments();
if ($offersCount < 3) {
    $db->offers->insertMany([
        [
            'type' => 'city',
            'title' => 'Séjour découverte',
            'description' => 'Une expérience de 3 jours avec activités et hébergement.',
            'price' => 199.0,
            'available' => true,
            'photos' => [],
        ],
        [
            'type' => 'adventure',
            'title' => 'Aventure & sensations',
            'description' => 'Un pack aventure pour les amateurs de sensations fortes.',
            'price' => 299.0,
            'available' => true,
            'photos' => [],
        ],
    ]);
    echo "Seed: offers inserted\n";
}
