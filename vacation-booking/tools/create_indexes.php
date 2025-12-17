<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/config/mongo.php';

try {
    $db->users->createIndex(['email' => 1], ['unique' => true]);
    echo "OK: created unique index users.email\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
