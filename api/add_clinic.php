<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Superadmin') {
    die("Unauthorized access.");
}
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic_name = trim($_POST['clinic_name']);
    $admin_username = trim($_POST['admin_username']);
    $admin_email = trim($_POST['admin_email']);
    $admin_password = $_POST['admin_password'];
    $subscription_price = $_POST['subscription_price'];
    $subscription_expiry = $_POST['subscription_expiry'];

    if (empty($clinic_name) || empty($admin_username) || empty($admin_password)) {
        header("Location: ../superadmin.php?msg=" . urlencode("Missing required fields."));
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Create Clinic
        $stmt = $pdo->prepare("INSERT INTO clinics (name, subscription_status, subscription_price, subscription_expiry) VALUES (?, 'Active', ?, ?)");
        $stmt->execute([$clinic_name, $subscription_price, $subscription_expiry]);
        $new_clinic_id = $pdo->lastInsertId();

        // 2. Create Admin User for this Clinic
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        $stmtUser = $pdo->prepare("INSERT INTO users (username, email, full_name, password, role, clinic_id) VALUES (?, ?, ?, ?, 'Admin', ?)");
        $stmtUser->execute([$admin_username, $admin_email, 'Clinic Administrator', $hashed_password, $new_clinic_id]);

        $pdo->commit();
        header("Location: ../superadmin.php?msg=" . urlencode("Success: Clinic '$clinic_name' registered!"));
    } catch (PDOException $e) {
        $pdo->rollBack();
        // Check for duplicate username
        if ($e->getCode() == 23000) {
            $errorMsg = "Error: Username '$admin_username' is already taken.";
        } else {
            $errorMsg = "Database error: " . $e->getMessage();
        }
        header("Location: ../superadmin.php?msg=" . urlencode($errorMsg));
    }
} else {
    header("Location: ../superadmin.php");
}
?>
