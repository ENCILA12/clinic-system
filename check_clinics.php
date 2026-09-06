<?php
require_once 'includes/db.php';
$stmt = $pdo->query("SHOW COLUMNS FROM clinics");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
