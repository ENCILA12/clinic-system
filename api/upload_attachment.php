<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'] ?? '';
    $file_type = $_POST['file_type'] ?? 'Other';
    
    if (empty($patient_id) || !isset($_FILES['attachment_file']) || $_FILES['attachment_file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Invalid file upload.']);
        exit;
    }

    $file = $_FILES['attachment_file'];
    $originalName = basename($file['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    
    // Security: allow only certain types
    $allowed = ['jpg','jpeg','png','gif','webp','pdf','doc','docx'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'File type not allowed.']);
        exit;
    }

    // Create unique filename
    $newName = uniqid('ATT_') . '_' . time() . '.' . $ext;
    $targetDir = '../uploads/attachments/';
    $targetFile = $targetDir . $newName;
    $dbPath = 'uploads/attachments/' . $newName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO attachments (patient_id, clinic_id, file_type, file_name, file_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$patient_id, $_SESSION['clinic_id'], $file_type, $originalName, $dbPath]);
            echo json_encode(['success' => true, 'message' => 'File uploaded successfully!']);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
    }
}
?>
