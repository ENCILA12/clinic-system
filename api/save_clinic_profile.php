<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['clinic_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic_name = trim($_POST['clinic_name'] ?? '');
    
    if (empty($clinic_name)) {
        echo json_encode(['success' => false, 'message' => 'Clinic name is required.']);
        exit;
    }

    $clinic_id = $_SESSION['clinic_id'];
    $logo_url = null;

    // Handle File Upload
    if (isset($_FILES['clinic_logo']) && $_FILES['clinic_logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/logos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileInfo = pathinfo($_FILES['clinic_logo']['name']);
        $ext = strtolower($fileInfo['extension']);
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowedExts)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file format. Only images are allowed.']);
            exit;
        }

        $newFileName = 'clinic_' . $clinic_id . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['clinic_logo']['tmp_name'], $destPath)) {
            $logo_url = 'uploads/logos/' . $newFileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
            exit;
        }
    }

    try {
        if ($logo_url) {
            $stmt = $pdo->prepare("UPDATE clinics SET name = ?, logo_url = ? WHERE id = ?");
            $stmt->execute([$clinic_name, $logo_url, $clinic_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE clinics SET name = ? WHERE id = ?");
            $stmt->execute([$clinic_name, $clinic_id]);
        }
        
        $_SESSION['clinic_name'] = $clinic_name;
        if ($logo_url) {
            $_SESSION['clinic_logo'] = $logo_url;
        }

        echo json_encode(['success' => true, 'message' => 'Clinic profile updated successfully!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
