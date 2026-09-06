<?php
require_once 'includes/db.php';
$stmt = $pdo->query("SHOW COLUMNS FROM billing");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
$stmt2 = $pdo->query("SHOW COLUMNS FROM treatments");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
?>
