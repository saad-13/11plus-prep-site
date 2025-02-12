<?php

$host = 'localhost';
$db   = '11plus_db';
$user = 'root';       // 
$pass = '';           // 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Enable exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Fetch associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Disable emulated prepares
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Handle connection error (in production, log the error instead of displaying)
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
