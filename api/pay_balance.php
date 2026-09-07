<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Receptionist'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_id = $_POST['invoice_id'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);

    if (empty($invoice_id) || $amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid invoice or payment amount.']);
        exit;
    }

    try {
        // Fetch current billing record
        $stmt = $pdo->prepare("SELECT total_amount, amount_paid, balance FROM billing WHERE invoice_id = ? AND clinic_id = ?");
        $stmt->execute([$invoice_id, $_SESSION['clinic_id']]);
        $bill = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$bill) {
            echo json_encode(['success' => false, 'message' => 'Invoice not found.']);
            exit;
        }

        $new_paid = $bill['amount_paid'] + $amount;
        $new_balance = $bill['total_amount'] - $new_paid;
        
        if ($new_balance < 0) {
            $new_balance = 0; // Prevent negative balance
        }

        $status = ($new_balance <= 0) ? 'Paid' : 'Partial';

        $update = $pdo->prepare("UPDATE billing SET amount_paid = ?, balance = ?, payment_status = ? WHERE invoice_id = ? AND clinic_id = ?");
        $update->execute([$new_paid, $new_balance, $status, $invoice_id, $_SESSION['clinic_id']]);

        echo json_encode(['success' => true, 'message' => 'Payment updated successfully.', 'new_balance' => $new_balance]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
