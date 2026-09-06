<?php
require_once 'includes/db.php';

$tables_query = $pdo->query("SHOW TABLES");
$tables = $tables_query->fetchAll(PDO::FETCH_COLUMN);

$schema = [];
foreach ($tables as $table) {
    $columns_query = $pdo->query("SHOW COLUMNS FROM `$table`");
    $schema[$table] = $columns_query->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($schema, JSON_PRETTY_PRINT);
?>
