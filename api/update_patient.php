<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['patient_id'])) {
        echo json_encode(['success' => false, 'message' => 'Patient ID is missing.']);
        exit;
    }

    $sql = "UPDATE patients SET 
                full_name = :full_name,
                birthday = :birthday,
                age = :age,
                gender = :gender,
                contact_number = :contact_number,
                email = :email,
                address = :address,
                emergency_contact = :emergency_contact,
                occupation = :occupation,
                blood_type = :blood_type,
                smoking_status = :smoking_status,
                allergies = :allergies,
                medical_conditions = :medical_conditions,
                current_medications = :current_medications,
                pregnancy = :pregnancy
            WHERE patient_id = :patient_id AND clinic_id = :clinic_id";
            
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            ':patient_id' => $_POST['patient_id'],
            ':clinic_id' => $_SESSION['clinic_id'],
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
        
        echo json_encode(['success' => true, 'message' => 'Patient profile updated successfully!']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error updating patient: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
