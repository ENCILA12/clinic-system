<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['clinic_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$clinic_id = $_SESSION['clinic_id'];
$notifications = [];

try {
    // 1. Today's Appointments
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE clinic_id = ? AND appointment_date = ? AND status = 'Pending'");
    $stmt->execute([$clinic_id, $today]);
    $apptCount = $stmt->fetchColumn();

    if ($apptCount > 0) {
        $notifications[] = [
            'title' => 'Appointments Today',
            'message' => "You have $apptCount pending appointment(s) today.",
            'icon' => 'fa-calendar-day',
            'link' => 'appointments.php'
        ];
    }

    // 2. Low Stock Inventory
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM inventory WHERE clinic_id = ? AND current_stock <= minimum_stock");
    $stmt->execute([$clinic_id]);
    $lowStockCount = $stmt->fetchColumn();

    if ($lowStockCount > 0) {
        $notifications[] = [
            'title' => 'Low Stock Alert',
            'message' => "You have $lowStockCount item(s) running low on stock.",
            'icon' => 'fa-boxes-stacked',
            'link' => 'inventory.php'
        ];
    }

    // 3. Expiring Inventory
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM inventory WHERE clinic_id = ? AND expiration_date IS NOT NULL AND expiration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute([$clinic_id]);
    $expiringCount = $stmt->fetchColumn();

    if ($expiringCount > 0) {
        $notifications[] = [
            'title' => 'Expiring Items',
            'message' => "You have $expiringCount item(s) expiring within 30 days.",
            'icon' => 'fa-triangle-exclamation',
            'link' => 'inventory.php'
        ];
    }

    echo json_encode([
        'success' => true,
        'count' => count($notifications),
        'notifications' => $notifications
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
