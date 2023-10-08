<?php

$host = 'localhost';
$dbname = 'bryan_ferrando_web_day10';
$username = 'root';
$password = '';

try {
  $pdo = new PDO("mysql:host=$host", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  response(['success' => false, 'error' => 'An error occured'], 500);
}

$sql = <<<SQL
  CREATE DATABASE IF NOT EXISTS $dbname
SQL;

$pdo->query($sql);
$pdo->query("use $dbname");

function generateTable($pdo, $dbname, $table, $sql){
  $createTableSQL = <<<SQL
    SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
  SQL;
  
  $result = fetchAll($pdo, $createTableSQL, [$dbname, $table]);
  
  if(count($result) == 0){
    $pdo->query($sql);
  }
}