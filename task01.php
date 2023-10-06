<?php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'error' => 'An error occured']);
    exit;
}

$name = $_GET['name'] ?? null;

if (!$name) {
    echo json_encode(['success' => false, 'error' => 'An error occured']);
    exit;
}

echo json_encode(['success' => true, 'name' => $name]);