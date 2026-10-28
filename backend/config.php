<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
//session dump for debugging
//echo '<pre>';
//print_r($_SESSION);
//echo '</pre>';  
$pdo = new PDO(
    'mysql:host=localhost;dbname=pendu;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

$dbh = $pdo;
?>