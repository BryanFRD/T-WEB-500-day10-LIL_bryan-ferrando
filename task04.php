<?php

header('Content-Type: application/json');
require('db/connection.php');

$body = json_decode(file_get_contents('php://input'), true);

if(!isset($body)){
  response(['success' => false, 'error' => 'No data provided'], 400);
}

$type = $body['type'] ?? null;
$brand = $body['brand'] ?? null;

if(empty($type) || empty($brand)){
  response(['success' => false, 'error' => 'Missing data'], 400);
}

$sql = 'INSERT INTO products (type, brand) VALUES (:type, :brand)';
$stmt = $pdo->prepare($sql);
$stmt->execute(['type' => $type, 'brand' => $brand]);

if($stmt->rowCount() === 0){
  response(['success' => false, 'error' => 'No product added'], 400);
}

response(['success' => true, 'message' => 'Product added']);

function response($data, $code = 200){
  http_response_code($code);
  echo json_encode($data);
  exit;
}