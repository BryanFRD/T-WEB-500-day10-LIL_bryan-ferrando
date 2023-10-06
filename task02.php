<?php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'An error occured']);
    http_response_code(400);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$email = $body['email'] ?? null;

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Email is not valid']);
    http_response_code(400);
    exit;
}

http_response_code(200);

echo json_encode(['success' => true, 'email' => $email]);