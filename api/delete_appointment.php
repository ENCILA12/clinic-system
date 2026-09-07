<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ? AND clinic_id = ?");
            $stmt->execute([$id, $_SESSION['clinic_id']]);
            echo json_encode(['success' => true, 'message' => 'Appointment deleted.']);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
    }
}
?>
