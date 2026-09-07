<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['clinic_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic_id = $_SESSION['clinic_id'];
    $category = trim($_POST['category'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $expense_date = $_POST['expense_date'] ?? date('Y-m-d');
    $description = trim($_POST['description'] ?? '');

    if (empty($category) || $amount <= 0) {
        echo json_encode(['success' => false, 'message' => 'Valid category and amount are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO expenses (clinic_id, category, amount, description, expense_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$clinic_id, $category, $amount, $description, $expense_date]);
        echo json_encode(['success' => true, 'message' => 'Expense saved!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
