<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'] ?? '';
    $subtotal = floatval($_POST['subtotal'] ?? 0);
    $discount = floatval($_POST['discount'] ?? 0);
    $total_amount = floatval($_POST['total_amount'] ?? 0);
    $amount_paid = floatval($_POST['amount_paid'] ?? 0);
    $balance = floatval($_POST['balance'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? 'Cash';
    $hmo_provider = $_POST['hmo_provider'] ?? null;
    $hmo_approval_code = $_POST['hmo_approval_code'] ?? null;
    
    // Determine status
    $payment_status = 'Unpaid';
    if ($payment_method === 'HMO') {
        $payment_status = 'For HMO Claim';
    } else {
        if ($amount_paid > 0) {
            if ($balance <= 0) {
                $payment_status = 'Paid';
            } else {
                $payment_status = 'Partial';
            }
        }
    }

    if (empty($patient_id)) {
        echo json_encode(['success' => false, 'message' => 'Please select a patient.']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Generate Invoice ID
        $stmt = $pdo->query("SELECT id FROM billing ORDER BY id DESC LIMIT 1");
        $lastBill = $stmt->fetch();
        $nextId = $lastBill ? $lastBill['id'] + 1 : 1;
        $invoice_id = "INV-" . date('ym') . "-" . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // Insert into billing
        $sql = "INSERT INTO billing (
                    invoice_id, patient_id, subtotal, discount, total_amount, 
                    amount_paid, balance, payment_method, payment_status,
                    hmo_provider, hmo_approval_code
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $invoice_id, $patient_id, $subtotal, $discount, $total_amount, 
            $amount_paid, $balance, $payment_method, $payment_status,
            $hmo_provider, $hmo_approval_code
        ]);

        // Insert billing items
        // In $_POST arrays: service_name[], qty[], price[]
        if (isset($_POST['service_name']) && is_array($_POST['service_name'])) {
            $stmtItem = $pdo->prepare("INSERT INTO billing_items (invoice_id, service_name, quantity, price) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < count($_POST['service_name']); $i++) {
                $s_name = $_POST['service_name'][$i];
                $s_qty = intval($_POST['qty'][$i]);
                $s_price = floatval($_POST['price'][$i]);
                
                if (!empty($s_name) && $s_qty > 0) {
                    $stmtItem->execute([$invoice_id, $s_name, $s_qty, $s_price]);
                }
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Invoice created successfully!']);
    } catch(PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
