<?php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'An error occured']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? null;

if (!$name) {
    echo json_encode(['success' => false, 'error' => 'An error occured']);
    exit;
}

echo json_encode(['success' => true, 'name' => $name]);