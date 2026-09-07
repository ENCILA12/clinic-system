<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $item_name = trim($_POST['item_name'] ?? '');
    $current_stock = intval($_POST['current_stock'] ?? 0);
    $minimum_stock = intval($_POST['minimum_stock'] ?? 10);
    $expiration_date = $_POST['expiration_date'] ?? '';

    if (empty($expiration_date)) $expiration_date = null;

    if (empty($item_name)) {
        echo json_encode(['success' => false, 'message' => 'Item name is required.']);
        exit;
    }

    try {
        if (!empty($id)) {
            // Update
            $sql = "UPDATE inventory SET 
                    item_name = ?, current_stock = ?, minimum_stock = ?, expiration_date = ? 
                    WHERE id = ? AND clinic_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$item_name, $current_stock, $minimum_stock, $expiration_date, $id, $_SESSION['clinic_id']]);
            $msg = 'Item updated successfully!';
        } else {
            // Insert
            $sql = "INSERT INTO inventory (clinic_id, item_name, current_stock, minimum_stock, expiration_date) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_SESSION['clinic_id'], $item_name, $current_stock, $minimum_stock, $expiration_date]);
            $msg = 'Item added successfully!';
        }

        echo json_encode(['success' => true, 'message' => $msg]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
