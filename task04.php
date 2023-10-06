<?php

header('Content-Type: application/json');
require('db/connection.php');

$type = strtolower($_GET['type'] ?? '');
$brand = strtolower($_GET['brand'] ?? '');

$typeRegex = [
  ["regex" => "/^.{3,}$/", "error" => ": This type does not have enough characters."],
  ["regex" => "/^.{0,10}$/", "error" => ": This type has too many characters."],
  ["regex" => "/^[a-zA-Z-]*$/", "error" => ": This type has non-alphabetical characters (different from '-')."]
];

$brandRegex = [
  ["regex" => "/^.{2,}$/", "error" => ": This brand does not have enough characters."],
  ["regex" => "/^.{0,20}$/", "error" => ": This brand has too many characters."],
  ["regex" => "/^[a-zA-Z0-9&-]*$/", "error" => ": Only allows alphanumeric characters, - and &"]
];

if(empty($type)){
  response(['success' => false, 'error' => 'No type sent yet!'], 400);
}

if(empty($brand)){
  response(['success' => false, 'error' => 'No brand sent yet!'], 400);
}

foreach($typeRegex as $regex){
  if(!preg_match($regex['regex'], $type)){
    response(['success' => false, 'error' => $type . $regex['error']], 400);
  }
}

foreach($brandRegex as $regex){
  if(!preg_match($regex['regex'], $brand)){
    response(['success' => false, 'error' => $brand . $regex['error']], 400);
  }
}

$sql = <<<SQL
  SELECT * from ajax_products.products WHERE type = ? AND brand = ?
SQL;

$stmt = $pdo->prepare($sql);
$stmt->execute([$type, $brand]);
$result = $stmt->fetchAll();

if(count($result) > 0){
  response(['success' => false, 'error' => "$brand: This brand with type '$type' already exist!"], 400);
}

response(['success' => true, 'message' => 'Data is valid.'], 200);

function response($data, $code = 200){
  http_response_code($code);
  echo json_encode($data);
  exit;
}