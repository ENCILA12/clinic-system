<?php
require_once 'includes/db.php';
try {
    $pdo->exec('ALTER TABLE clinics ADD COLUMN logo_url VARCHAR(255) DEFAULT NULL');
    echo "Success";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column already exists";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
