<?php
require_once 'includes/db.php';

try {
    $pdo->beginTransaction();

    // 1. Create clinics table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `clinics` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `subscription_status` varchar(50) DEFAULT 'ACTIVE',
            `created_at` timestamp DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Insert default clinic if not exists
    $stmt = $pdo->query("SELECT id FROM clinics WHERE id = 1");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("INSERT INTO clinics (id, name) VALUES (1, 'Main Clinic')");
    }

    $tables = [
        'appointments', 'attachments', 'billing', 'billing_items', 
        'dental_records', 'dentists', 'inventory', 'patients', 
        'treatments', 'users'
    ];

    foreach ($tables as $table) {
        // Check if clinic_id exists
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE 'clinic_id'");
        if ($stmt->rowCount() == 0) {
            // Add clinic_id column
            $pdo->exec("ALTER TABLE `$table` ADD `clinic_id` INT(11) NOT NULL DEFAULT 1");
            
            // Add foreign key constraint
            $pdo->exec("ALTER TABLE `$table` ADD CONSTRAINT `fk_{$table}_clinic` FOREIGN KEY (`clinic_id`) REFERENCES `clinics`(`id`) ON DELETE CASCADE");
            
            echo "Added clinic_id to $table\n";
        } else {
            echo "clinic_id already exists in $table\n";
        }
    }

    $pdo->commit();
    echo "\nSaaS Database Architecture Updated Successfully!";
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error setting up SaaS database: " . $e->getMessage());
}
?>
