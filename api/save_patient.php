<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate a Patient ID
    $stmt = $pdo->query("SELECT id FROM patients ORDER BY id DESC LIMIT 1");
    $lastPatient = $stmt->fetch();
    $nextId = $lastPatient ? $lastPatient['id'] + 1 : 1;
    $patient_id = "PT-" . date('ym') . "-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);

    $sql = "INSERT INTO patients (
                clinic_id, patient_id, full_name, birthday, age, gender, contact_number, email, address, 
                emergency_contact, occupation, blood_type, smoking_status, allergies, 
                medical_conditions, current_medications, pregnancy
            ) VALUES (
                :clinic_id, :patient_id, :full_name, :birthday, :age, :gender, :contact_number, :email, :address,
                :emergency_contact, :occupation, :blood_type, :smoking_status, :allergies,
                :medical_conditions, :current_medications, :pregnancy
            )";
            
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            ':clinic_id' => $_SESSION['clinic_id'] ?? 1,
            ':patient_id' => $patient_id,
            ':full_name' => $_POST['full_name'] ?? '',
            ':birthday' => $_POST['birthday'] ?? '',
            ':age' => $_POST['age'] ?? 0,
            ':gender' => $_POST['gender'] ?? '',
            ':contact_number' => $_POST['contact_number'] ?? '',
            ':email' => $_POST['email'] ?? '',
            ':address' => $_POST['address'] ?? '',
            ':emergency_contact' => $_POST['emergency_contact'] ?? '',
            ':occupation' => $_POST['occupation'] ?? '',
            ':blood_type' => $_POST['blood_type'] ?? '',
            ':smoking_status' => $_POST['smoking_status'] ?? '',
            ':allergies' => $_POST['allergies'] ?? '',
            ':medical_conditions' => $_POST['medical_conditions'] ?? '',
            ':current_medications' => $_POST['current_medications'] ?? '',
            ':pregnancy' => $_POST['pregnancy'] ?? ''
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Patient saved successfully!']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error saving patient: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
