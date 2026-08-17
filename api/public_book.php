<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $age = intval($_POST['age'] ?? 0);
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';
    $procedure_name = $_POST['procedure_name'] ?? '';
    $dentist_name = trim($_POST['dentist_name'] ?? '');
    $notes = $_POST['notes'] ?? '';

    if (empty($full_name) || empty($contact_number) || empty($appointment_date) || empty($appointment_time)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Check if patient exists by name and contact
        $stmtCheck = $pdo->prepare("SELECT patient_id FROM patients WHERE full_name = ? AND contact_number = ? LIMIT 1");
        $stmtCheck->execute([$full_name, $contact_number]);
        $existingPatient = $stmtCheck->fetch();

        if ($existingPatient) {
            $patient_id = $existingPatient['patient_id'];
        } else {
            // Create new patient record
            // Generate Patient ID
            $stmt = $pdo->query("SELECT id FROM patients ORDER BY id DESC LIMIT 1");
            $lastPatient = $stmt->fetch();
            $nextId = $lastPatient ? $lastPatient['id'] + 1 : 1;
            $patient_id = "PT-" . date('ym') . "-" . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            // Dummy birthday based on age (Jan 1 of birth year)
            $birthYear = date('Y') - $age;
            $birthday = "$birthYear-01-01";

            $sqlNewPatient = "INSERT INTO patients (patient_id, full_name, age, contact_number, birthday) VALUES (?, ?, ?, ?, ?)";
            $stmtNew = $pdo->prepare($sqlNewPatient);
            $stmtNew->execute([$patient_id, $full_name, $age, $contact_number, $birthday]);
        }

        // 2. Insert into appointments
        if (empty($dentist_name)) {
            $dentist_name = 'TBD (Any Available)';
        }

        $stmtAptId = $pdo->query("SELECT id FROM appointments ORDER BY id DESC LIMIT 1");
        $lastApt = $stmtAptId->fetch();
        $nextAptId = $lastApt ? $lastApt['id'] + 1 : 1;
        $appointment_id = "APT-" . date('ym') . "-" . str_pad($nextAptId, 3, '0', STR_PAD_LEFT);

        $sqlApt = "INSERT INTO appointments (
            appointment_id, patient_id, dentist_name, appointment_date, appointment_time, 
            procedure_name, estimated_duration, status, notes
        ) VALUES (
            ?, ?, ?, ?, ?, ?, 'TBD', 'Pending', ?
        )";
        $stmtApt = $pdo->prepare($sqlApt);
        // Note: From public booking, we add a prefix in notes so clinic knows
        $finalNotes = "PUBLIC BOOKING: " . $notes;
        $stmtApt->execute([
            $appointment_id, $patient_id, $dentist_name, $appointment_date, $appointment_time,
            $procedure_name, $finalNotes
        ]);

        $pdo->commit();
        echo json_encode(['success' => true]);

    } catch(PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
