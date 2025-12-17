<?php
include '../config/mongo.php';

$db->offers->insertOne([
    'type' => $_POST['type'],
    'title' => $_POST['title'],
    'description' => $_POST['description'],
    'price' => (int)$_POST['price'],
    'available' => true
]);

echo "Offre ajoutée";
