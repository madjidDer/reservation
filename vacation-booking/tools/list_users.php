<?php
require __DIR__ . '/../vendor/autoload.php';
$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->vacation_db;

header('Content-Type: text/plain; charset=utf-8');
foreach ($db->users->find() as $u) {
    echo "- id: " . (string)$u['_id'] . "\n";
    echo "  name: " . ($u['name'] ?? '') . "\n";
    echo "  email: " . ($u['email'] ?? '') . "\n";
    echo "  role: " . ($u['role'] ?? '') . "\n";
    echo "\n";
}
