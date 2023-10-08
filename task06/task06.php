<?php

header('Content-Type: application/json');
require('../db/connection.php');

$messagesTableSQL = file_get_contents("../resources/messages.sql");
generateTable($pdo, $dbname, 'messages', $messagesTableSQL);

$requestMethod = strtolower($_SERVER['REQUEST_METHOD']);

if(function_exists($requestMethod)){
  $requestMethod($pdo);
}

function get($pdo){
  $sql = <<<SQL
    SELECT * FROM bryan_ferrando_web_day10.messages LIMIT 50
  SQL;
  
  $result = fetchAll($pdo, $sql, []);
  
  response(['success' => true, 'messages' => $result]);
}

function post($pdo){
  $body = json_decode(file_get_contents('php://input'), true);
  
  $sql = <<<SQL
    INSERT INTO bryan_ferrando_web_day10.messages (username, message)
    VALUES (:username, :message)
  SQL;
  
  if(!isset($body['username']) || !isset($body['message'])){
    response(['success' => false, 'message' => 'Username or message not set!'], 400);
  }
  
  $result = query($pdo, $sql, $body);
  
  if(!$result){
    response(['success' => false, 'message' => 'Message not saved!'], 400);
  }
  
  response(['success' => true, 'message' => 'Message saved!']);
}

function fetchAll($pdo, $sql, $data){
  $stmt = $pdo->prepare($sql);
  $stmt->execute($data);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function query($pdo, $sql, $data){
  $stmt = $pdo->prepare($sql);
  $stmt->execute($data);
  return ($stmt->rowCount() > 0);
}

function response($data, $code = 200){
  http_response_code($code);
  echo json_encode($data);
  exit;
}