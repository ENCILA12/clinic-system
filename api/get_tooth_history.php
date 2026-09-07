<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
header('Content-Type: application/json');

if (isset($_GET['patient_id']) && isset($_GET['tooth_number'])) {
    $patient_id = $_GET['patient_id'];
    $tooth_number = $_GET['tooth_number'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM dental_records WHERE patient_id = ? AND tooth_number = ? AND clinic_id = ? ORDER BY created_at DESC");
        $stmt->execute([$patient_id, $tooth_number, $_SESSION['clinic_id']]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $history]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
}
?>
