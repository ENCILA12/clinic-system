<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter username and password.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Success
            // Fetch clinic name
            $stmtC = $pdo->prepare("SELECT name, subscription_expiry, logo_url FROM clinics WHERE id = ?");
            $stmtC->execute([$user['clinic_id']]);
            $clinicData = $stmtC->fetch(PDO::FETCH_ASSOC);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['clinic_id'] = $user['clinic_id'];
            $_SESSION['clinic_name'] = $clinicData ? $clinicData['name'] : 'DentaFlow';
            $_SESSION['clinic_logo'] = $clinicData ? $clinicData['logo_url'] : null;
            $_SESSION['subscription_expiry'] = $clinicData ? $clinicData['subscription_expiry'] : null;
            
            $redirectUrl = ($user['role'] === 'Superadmin') ? 'superadmin.php' : 'index.php';
            
            echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
}
?>
