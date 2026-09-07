<?php
// Set PHP timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

$host = 'localhost';
$dbname = 'clinic_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set MySQL timezone to Philippine Time
    $pdo->exec("SET time_zone = '+08:00'");
} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
