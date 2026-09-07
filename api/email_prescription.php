<?php
session_start();
if (!isset($_SESSION['clinic_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

require_once '../includes/db.php';

$px_id = $_GET['id'];
$clinic_id = $_SESSION['clinic_id'];
$clinic_name = $_SESSION['clinic_name'] ?? 'Dental Clinic';

try {
    $stmtPx = $pdo->prepare("
        SELECT p.*, pt.full_name, pt.email 
        FROM prescriptions p 
        JOIN patients pt ON p.patient_id = pt.patient_id 
        WHERE p.id = ? AND p.clinic_id = ?
    ");
    $stmtPx->execute([$px_id, $clinic_id]);
    $px = $stmtPx->fetch(PDO::FETCH_ASSOC);

    if (!$px) {
        echo json_encode(['success' => false, 'message' => 'Prescription not found.']);
        exit;
    }

    if (empty($px['email'])) {
        echo json_encode(['success' => false, 'message' => 'Patient does not have a registered email address. Please update their profile.']);
        exit;
    }

    $stmtMeds = $pdo->prepare("SELECT * FROM prescription_items WHERE prescription_id = ?");
    $stmtMeds->execute([$px_id]);
    $meds = $stmtMeds->fetchAll(PDO::FETCH_ASSOC);

    // Build Email Content
    $to = $px['email'];
    $subject = "Your E-Prescription from " . $clinic_name;
    
    $message = "<html><body>";
    $message .= "<h2>Hello " . htmlspecialchars($px['full_name']) . ",</h2>";
    $message .= "<p>Here is your digital prescription issued by " . htmlspecialchars($px['dentist_name']) . " on " . date('M d, Y', strtotime($px['created_at'])) . ".</p>";
    
    $message .= "<h3>Medicines (Rx):</h3><ul>";
    foreach($meds as $m) {
        $message .= "<li><strong>" . htmlspecialchars($m['medicine_name']) . " " . htmlspecialchars($m['dosage']) . "</strong><br>";
        $message .= "Take " . htmlspecialchars($m['frequency']) . " for " . htmlspecialchars($m['duration']) . "</li>";
    }
    $message .= "</ul>";

    if (!empty($px['notes'])) {
        $message .= "<p><strong>Doctor's Notes:</strong><br>" . nl2br(htmlspecialchars($px['notes'])) . "</p>";
    }

    // Generate a direct public link if desired (assuming standard URL structure)
    // $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    // $host = $_SERVER['HTTP_HOST'];
    // $url = $protocol . "://" . $host . "/print_prescription.php?id=" . $px_id;
    // $message .= "<br><p>To print or view your official PDF, <a href='$url'>click here</a>.</p>";

    $message .= "<br><p>Thank you,<br>" . htmlspecialchars($clinic_name) . "</p>";
    $message .= "</body></html>";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@clinic.tazabrew.com" . "\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email. Check your server mail configuration.']);
    }

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>
