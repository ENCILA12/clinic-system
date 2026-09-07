<?php
session_start();
require_once '../includes/db.php';

// Verify that the current user is a Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Superadmin') {
    die("Unauthorized access. Only Superadmins can perform this action.");
}

if (!isset($_GET['clinic_id'])) {
    die("Clinic ID missing.");
}

$clinic_id = $_GET['clinic_id'];

try {
    // Find the primary Admin for this clinic
    $stmt = $pdo->prepare("SELECT * FROM users WHERE clinic_id = ? AND role = 'Admin' ORDER BY id ASC LIMIT 1");
    $stmt->execute([$clinic_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        die("No Admin account found for this clinic. Please create one first.");
    }

    // Fetch clinic details for the session
    $stmtC = $pdo->prepare("SELECT name, slug, logo_url FROM clinics WHERE id = ?");
    $stmtC->execute([$clinic_id]);
    $clinic = $stmtC->fetch(PDO::FETCH_ASSOC);

    // Overwrite session with the clinic Admin's data
    $_SESSION['user_id'] = $admin['id'];
    $_SESSION['username'] = $admin['username'];
    $_SESSION['role'] = $admin['role'];
    $_SESSION['clinic_id'] = $admin['clinic_id'];
    $_SESSION['clinic_name'] = $clinic ? $clinic['name'] : 'Unknown Clinic';
    $_SESSION['clinic_slug'] = $clinic ? $clinic['slug'] : '';
    $_SESSION['clinic_logo'] = $clinic ? $clinic['logo_url'] : null;
    
    // Set a flag to remind them they are in override mode
    $_SESSION['superadmin_override'] = true;

    $redirectUrl = empty($clinic['slug']) ? '../index.php' : '../' . $clinic['slug'] . '/index.php';
    header("Location: " . $redirectUrl);
    exit;

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
