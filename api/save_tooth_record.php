<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'] ?? '';
    $tooth_number = $_POST['tooth_number'] ?? '';
    $status = $_POST['status'] ?? 'Healthy';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $treatment = $_POST['treatment'] ?? '';
    $dentist_name = $_POST['dentist_name'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (empty($patient_id) || empty($tooth_number)) {
        echo json_encode(['success' => false, 'message' => 'Missing patient or tooth number.']);
        exit;
    }

    $sql = "INSERT INTO dental_records (
                patient_id, tooth_number, status, diagnosis, treatment, dentist_name, notes
            ) VALUES (
                :patient_id, :tooth_number, :status, :diagnosis, :treatment, :dentist_name, :notes
            )";
            
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            ':patient_id' => $patient_id,
            ':tooth_number' => $tooth_number,
            ':status' => $status,
            ':diagnosis' => $diagnosis,
            ':treatment' => $treatment,
            ':dentist_name' => $dentist_name,
            ':notes' => $notes
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Tooth record saved successfully!']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
    }
}
?>
