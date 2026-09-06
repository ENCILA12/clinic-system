<?php
require_once 'includes/db.php';

try {
    // 1. Add email to users
    $stmt = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'email'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `users` ADD `email` VARCHAR(150) NULL AFTER `username`");
    }

    // 2. Add subscription columns to clinics
    $stmt = $pdo->query("SHOW COLUMNS FROM `clinics` LIKE 'subscription_expiry'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `clinics` ADD `subscription_expiry` DATE NULL AFTER `subscription_status`");
        $pdo->exec("ALTER TABLE `clinics` ADD `subscription_price` DECIMAL(10,2) DEFAULT 0.00 AFTER `subscription_expiry`");
    }

    // 3. Create superadmin account if not exists
    $stmt = $pdo->query("SELECT id FROM users WHERE username = 'superadmin'");
    if ($stmt->rowCount() == 0) {
        $passwordHash = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, full_name, password, role, clinic_id) VALUES (?, ?, ?, ?, NULL)");
        // clinic_id is NULL for superadmin because they own the system
        // Wait, clinic_id is NOT NULL DEFAULT 1 according to our setup_saas.php
        // Let's modify clinic_id to allow NULL for superadmin, or just assign them to clinic 1 but role = Superadmin.
        // Allowing NULL is better architecture.
        $pdo->exec("ALTER TABLE `users` MODIFY `clinic_id` INT(11) NULL");
        
        $stmt->execute(['superadmin', 'System Owner', $passwordHash, 'Superadmin']);
    }

    echo "Superadmin database migration completed successfully.";
} catch (Exception $e) {
    die("Database Migration Error: " . $e->getMessage());
}
?>
