<?php
$host = 'localhost';  // MySQL server on windows from WSL
$db   = 'milos';       // DB name
$user = 'root';       // MySQL user
$pass = 'root';   // user password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    //echo "Connected to demo_shop database!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>
