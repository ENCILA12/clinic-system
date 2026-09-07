<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['clinic_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic_id = $_SESSION['clinic_id'];
    $patient_id = $_POST['patient_id'] ?? '';
    $dentist_name = $_POST['dentist_name'] ?? '';
    $notes = trim($_POST['notes'] ?? '');

    $medicines = $_POST['medicine_name'] ?? [];
    $dosages = $_POST['dosage'] ?? [];
    $frequencies = $_POST['frequency'] ?? [];
    $durations = $_POST['duration'] ?? [];

    if (empty($patient_id) || empty($medicines)) {
        echo json_encode(['success' => false, 'message' => 'Required fields missing.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO prescriptions (clinic_id, patient_id, dentist_name, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$clinic_id, $patient_id, $dentist_name, $notes]);
        $prescription_id = $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("INSERT INTO prescription_items (prescription_id, medicine_name, dosage, frequency, duration) VALUES (?, ?, ?, ?, ?)");
        
        for ($i = 0; $i < count($medicines); $i++) {
            if (!empty(trim($medicines[$i]))) {
                $stmtItem->execute([
                    $prescription_id, 
                    trim($medicines[$i]), 
                    trim($dosages[$i] ?? ''), 
                    trim($frequencies[$i] ?? ''), 
                    trim($durations[$i] ?? '')
                ]);
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'prescription_id' => $prescription_id]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
