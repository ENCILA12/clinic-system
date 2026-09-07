<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT id FROM treatments WHERE clinic_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$_SESSION['clinic_id']]);
    $lastTx = $stmt->fetch();
    $nextId = $lastTx ? $lastTx['id'] + 1 : 1;
    $treatment_id = "TX-" . date('ym') . "-" . str_pad($nextId, 3, '0', STR_PAD_LEFT);

    $follow_up = $_POST['follow_up_date'] ?? '';
    if (empty($follow_up)) $follow_up = null;

    try {
        $pdo->beginTransaction();

        // 1. Process Materials & Deduct Inventory
        $materialsText = '';
        if (isset($_POST['material_id']) && is_array($_POST['material_id'])) {
            $usedList = [];
            $stmtUpdateInv = $pdo->prepare("UPDATE inventory SET current_stock = current_stock - :qty WHERE id = :id AND clinic_id = :clinic_id AND current_stock >= :qty");
            
            for ($i = 0; $i < count($_POST['material_id']); $i++) {
                $m_id = $_POST['material_id'][$i];
                $m_qty = intval($_POST['material_qty'][$i]);
                $m_name = $_POST['material_name'][$i];
                
                if ($m_qty > 0) {
                    $stmtUpdateInv->execute([':qty' => $m_qty, ':id' => $m_id, ':clinic_id' => $_SESSION['clinic_id']]);
                    $usedList[] = "$m_name (x$m_qty)";
                }
            }
            if (count($usedList) > 0) {
                $materialsText = implode(', ', $usedList);
            }
        }

        // 2. Insert Treatment Record
        $sql = "INSERT INTO treatments (
                    clinic_id, treatment_id, patient_id, dentist_name, chief_complaint, 
                    diagnosis, treatment_plan, procedure_done, follow_up_date, notes, materials_used
                ) VALUES (
                    :clinic_id, :treatment_id, :patient_id, :dentist_name, :chief_complaint,
                    :diagnosis, :treatment_plan, :procedure_done, :follow_up_date, :notes, :materials_used
                )";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':clinic_id' => $_SESSION['clinic_id'] ?? 1,
            ':treatment_id' => $treatment_id,
            ':patient_id' => $_POST['patient_id'] ?? '',
            ':dentist_name' => $_POST['dentist_name'] ?? '',
            ':chief_complaint' => $_POST['chief_complaint'] ?? '',
            ':diagnosis' => $_POST['diagnosis'] ?? '',
            ':treatment_plan' => $_POST['treatment_plan'] ?? '',
            ':procedure_done' => $_POST['procedure_done'] ?? '',
            ':follow_up_date' => $follow_up,
            ':notes' => $_POST['notes'] ?? '',
            ':materials_used' => $materialsText ?: null
        ]);
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Treatment record saved & inventory updated successfully!']);
    } catch(PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error saving treatment: ' . $e->getMessage()]);
    }
}
?>
