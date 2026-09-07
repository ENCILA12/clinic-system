<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->query("SELECT id FROM appointments ORDER BY id DESC LIMIT 1");
    $lastApt = $stmt->fetch();
    $nextId = $lastApt ? $lastApt['id'] + 1 : 1;
    $appointment_id = "APT-" . date('ym') . "-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);

    $sql = "INSERT INTO appointments (
                clinic_id, appointment_id, patient_id, dentist_name, appointment_date, appointment_time, 
                procedure_name, estimated_duration, notes
            ) VALUES (
                :clinic_id, :appointment_id, :patient_id, :dentist_name, :appointment_date, :appointment_time,
                :procedure_name, :estimated_duration, :notes
            )";
            
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            ':appointment_id' => $appointment_id,
            ':patient_id' => $_POST['patient_id'] ?? '',
            ':dentist_name' => $_POST['dentist_name'] ?? '',
            ':appointment_date' => $_POST['appointment_date'] ?? '',
            ':appointment_time' => $_POST['appointment_time'] ?? '',
            ':procedure_name' => $_POST['procedure_name'] ?? '',
            ':estimated_duration' => $_POST['estimated_duration'] ?? '',
            ':notes' => $_POST['notes'] ?? '',
            ':clinic_id' => $_SESSION['clinic_id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Appointment booked successfully!']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error booking appointment: ' . $e->getMessage()]);
    }
}
?>
