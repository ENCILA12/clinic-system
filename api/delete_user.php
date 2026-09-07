<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Missing ID.']);
        exit;
    }

    try {
        // Prevent deleting the main admin
        $checkStmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
        $checkStmt->execute([$id]);
        $user = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['username'] === 'admin') {
            echo json_encode(['success' => false, 'message' => 'Cannot delete the main admin account.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND clinic_id = ?");
        $stmt->execute([$id, $_SESSION['clinic_id']]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
}
?>
