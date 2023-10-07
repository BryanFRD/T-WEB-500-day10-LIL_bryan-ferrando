<?php

header('Content-Type: application/json');
require('db/connection.php');

$type = strtolower($_GET['type'] ?? '');
$brand = strtolower($_GET['brand'] ?? '');
$price = strtolower($_GET['price'] ?? '');
$stock = strtolower($_GET['number'] ?? '');

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

$priceRegex = [
  ["regex" => "/^[><=]/", "error" => "First character must be '>', '<' or '='."],
  ["regex" => "/\d{1,5}$/", "error" => "Its length needs to be between 2 and 5."]
];

$numberRegex = [
  ["regex" => "/^\d*\.?\d+$/", "error" => "Only allows positive numbers."]
];

if(empty($type)){
  response(['success' => false, 'error' => 'No type sent yet!'], 400);
}

if(empty($brand)){
  response(['success' => false, 'error' => 'No brand sent yet!'], 400);
}

if(empty($price)){
  response(['success' => false, 'error' => 'No price sent yet!'], 400);
}

if(empty($stock)){
  response(['success' => false, 'error' => 'No stock sent yet!'], 400);
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
  SELECT * from ajax_products.products WHERE type = ?
SQL;

$result = fetchAll($pdo, $sql, [$type]);

if(count($result) == 0){
  response(['success' => false, 'error' => "$type: This type doesn't exist in databases."], 400);
}

$sql = <<<SQL
  SELECT * from ajax_products.products WHERE brand = ?
SQL;

$result = fetchAll($pdo, $sql, [$brand]);

if(count($result) == 0){
  response(['success' => false, 'error' => "$brand: This brand exists in databases."], 400);
}

$sql = <<<SQL
  SELECT * from ajax_products.products WHERE brand = ?
SQL;

$result = fetchAll($pdo, $sql, [$brand]);

if(count($result) == 0){
  response(['success' => false, 'error' => "$brand: This brand doesn't exist in databases."]);
}

$sql = <<<SQL
  SELECT * from ajax_products.products WHERE type = ? AND brand = ?
SQL;

$result = fetchAll($pdo, $sql, [$type, $brand]);

if(count($result) == 0){
  response(['success' => false, 'error' => "$brand: This brand with type '$type' doesn't exist!"], 400);
}

$priceSeparator = $price[0];
$price = substr($price, 1);

$sql = <<<SQL
  SELECT *, (?) as number from ajax_products.products WHERE type = ? AND brand = ? AND price $priceSeparator ?
SQL;

$result = fetchAll($pdo, $sql, [$stock, $type, $brand, $price]);

if(count($result) == 0){
  response(['success' => false, 'error' => "$price: No products found at this price."], 400);
}

$minStock = null;
foreach(array_values($result) as $k => $product){
  $minStock = (!isset($minStock) || $minStock > $product['stock']) ? $product['stock'] : $minStock;
  
  if($product['stock'] < $stock){
    array_splice($result, $k, 1);
  }
}

if(count($result) == 0){
  response(['success' => false, 'error' => "$stock: Sorry, we don't have enough stock, we only have " . ($minStock ?? 0) . ' stock.'], 400);
}

response(['success' => true, 'products' => $result], 200);

function fetchAll($pdo, $sql, $data){
  $stmt = $pdo->prepare($sql);
  $stmt->execute($data);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function response($data, $code = 200){
  http_response_code($code);
  echo json_encode($data);
  exit;
}