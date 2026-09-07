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
            $clinic_slug = $_POST['clinic_slug'] ?? null;
            
            // If they are logging in via a slug, verify they belong to that clinic (unless they are superadmin)
            if ($clinic_slug && $user['role'] !== 'Superadmin') {
                $stmtSlug = $pdo->prepare("SELECT id FROM clinics WHERE slug = ?");
                $stmtSlug->execute([$clinic_slug]);
                $clinicFromSlug = $stmtSlug->fetch(PDO::FETCH_ASSOC);
                
                if (!$clinicFromSlug || $clinicFromSlug['id'] != $user['clinic_id']) {
                    echo json_encode(['success' => false, 'message' => 'Your account does not belong to this clinic.']);
                    exit;
                }
            }
            
            // If they are logging in from root (no slug), block non-superadmins
            if (!$clinic_slug && $user['role'] !== 'Superadmin') {
                echo json_encode(['success' => false, 'message' => 'Staff must log in using their clinic\'s specific URL.']);
                exit;
            }

            // Determine the active clinic ID
            $active_clinic_id = $user['clinic_id'];

            // If a Superadmin logs in via a clinic's slug, they adopt that clinic's context
            if ($clinic_slug && $user['role'] === 'Superadmin') {
                $stmtSlug = $pdo->prepare("SELECT id FROM clinics WHERE slug = ?");
                $stmtSlug->execute([$clinic_slug]);
                $clinicFromSlug = $stmtSlug->fetch(PDO::FETCH_ASSOC);
                if ($clinicFromSlug) {
                    $active_clinic_id = $clinicFromSlug['id'];
                }
            }

            // Success
            // Fetch clinic name
            $stmtC = $pdo->prepare("SELECT name, slug, subscription_expiry, logo_url FROM clinics WHERE id = ?");
            $stmtC->execute([$active_clinic_id]);
            $clinicData = $stmtC->fetch(PDO::FETCH_ASSOC);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['clinic_id'] = $active_clinic_id;
            $_SESSION['clinic_name'] = $clinicData ? $clinicData['name'] : 'DentaFlow';
            $_SESSION['clinic_slug'] = $clinicData ? $clinicData['slug'] : '';
            $_SESSION['clinic_logo'] = $clinicData ? $clinicData['logo_url'] : null;
            $_SESSION['subscription_expiry'] = $clinicData ? $clinicData['subscription_expiry'] : null;
            
            $redirectUrl = ($user['role'] === 'Superadmin' && !$clinic_slug) ? 'superadmin.php' : 'index.php';
            
            echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
}
?>
