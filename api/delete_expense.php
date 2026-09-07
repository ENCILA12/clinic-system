<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['clinic_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ? AND clinic_id = ?");
        $stmt->execute([$_GET['id'], $_SESSION['clinic_id']]);
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
