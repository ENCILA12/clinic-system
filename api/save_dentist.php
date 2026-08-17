<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $prc_license = trim($_POST['prc_license'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $consultation_fee = floatval($_POST['consultation_fee'] ?? 0);
    $is_available = isset($_POST['is_available']) ? (int)$_POST['is_available'] : 1;

    if (empty($full_name)) {
        echo json_encode(['success' => false, 'message' => 'Dentist name is required.']);
        exit;
    }

    try {
        if (!empty($id)) {
            // Update
            $sql = "UPDATE dentists SET 
                    full_name = ?, specialization = ?, prc_license = ?, schedule = ?, 
                    consultation_fee = ?, is_available = ? 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $specialization, $prc_license, $schedule, $consultation_fee, $is_available, $id]);
            $msg = 'Dentist updated successfully!';
        } else {
            // Insert
            $sql = "INSERT INTO dentists (full_name, specialization, prc_license, schedule, consultation_fee, is_available) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $specialization, $prc_license, $schedule, $consultation_fee, $is_available]);
            $msg = 'Dentist added successfully!';
        }

        echo json_encode(['success' => true, 'message' => $msg]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
