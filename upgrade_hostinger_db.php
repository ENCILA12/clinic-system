<?php
// upgrade_hostinger_db.php
require_once 'includes/db.php';

try {
    echo "<h2>Hostinger Database Upgrade Script</h2>";
    echo "<pre>";

    // 1. Ensure Clinics table exists with all modern columns
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `clinics` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `slug` varchar(255) DEFAULT NULL UNIQUE,
            `logo_url` varchar(255) DEFAULT NULL,
            `subscription_status` varchar(50) DEFAULT 'Active',
            `subscription_price` decimal(10,2) DEFAULT '0.00',
            `subscription_expiry` date DEFAULT NULL,
            `created_at` timestamp DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "1. Checked/Created 'clinics' table.\n";

    // Add new columns to clinics if they were missing (in case table already existed but was old)
    $clinic_columns = [
        'slug' => "VARCHAR(255) UNIQUE AFTER name",
        'logo_url' => "VARCHAR(255) DEFAULT NULL",
        'subscription_status' => "VARCHAR(50) DEFAULT 'Active'",
        'subscription_price' => "DECIMAL(10,2) DEFAULT '0.00'",
        'subscription_expiry' => "DATE DEFAULT NULL"
    ];

    foreach ($clinic_columns as $col => $def) {
        try {
            $pdo->exec("ALTER TABLE clinics ADD COLUMN $col $def");
            echo "   - Added missing column: $col\n";
        } catch (Exception $e) {
            // Column already exists, ignore
        }
    }

    // Insert default clinic if empty
    $stmt = $pdo->query("SELECT id FROM clinics WHERE id = 1");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("INSERT INTO clinics (id, name, slug) VALUES (1, 'Main Clinic', 'main-clinic')");
        echo "   - Inserted default Main Clinic.\n";
    }

    // 2. Ensure all data tables have clinic_id
    $tables = [
        'appointments', 'attachments', 'billing', 'billing_items', 
        'dental_records', 'dentists', 'inventory', 'patients', 
        'treatments', 'users', 'expenses', 'prescriptions'
    ];

    foreach ($tables as $table) {
        try {
            // Check if table exists first
            $checkTable = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($checkTable->rowCount() > 0) {
                $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE 'clinic_id'");
                if ($stmt->rowCount() == 0) {
                    $pdo->exec("ALTER TABLE `$table` ADD `clinic_id` INT(11) NOT NULL DEFAULT 1");
                    // Note: We skip foreign key constraint here to prevent errors with messy old data.
                    // The system will work fine with just the column.
                    echo "2. Added clinic_id to $table\n";
                }
            }
        } catch (Exception $e) {
            echo "   - Skipped $table (might not exist yet).\n";
        }
    }

    // 3. Generate slugs for any old clinics that don't have them
    $stmt = $pdo->query("SELECT id, name FROM clinics WHERE slug IS NULL OR slug = ''");
    $clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $updateStmt = $pdo->prepare("UPDATE clinics SET slug = ? WHERE id = ?");
    foreach ($clinics as $clinic) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $clinic['name'])));
        $slug = trim($slug, '-');
        $base_slug = $slug;
        $counter = 1;
        while (true) {
            $checkStmt = $pdo->prepare("SELECT id FROM clinics WHERE slug = ? AND id != ?");
            $checkStmt->execute([$slug, $clinic['id']]);
            if (!$checkStmt->fetch()) { break; }
            $slug = $base_slug . '-' . $counter;
            $counter++;
        }
        $updateStmt->execute([$slug, $clinic['id']]);
        echo "3. Generated slug '$slug' for Clinic ID {$clinic['id']}\n";
    }

    echo "\n<b>SUCCESS: Hostinger database has been successfully upgraded!</b>\n";
    echo "You can now safely delete this file (upgrade_hostinger_db.php) for security.</pre>";

} catch (PDOException $e) {
    die("Error upgrading database: " . $e->getMessage());
}
?>
