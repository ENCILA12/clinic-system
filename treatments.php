<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dental Clinic - Treatments</title>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <link rel='stylesheet' href='css/style.css'>
</head>
<body>
    <div class='dashboard-container'>
        <?php
 include 'includes/sidebar.php'; ?>
        <div class='main-content'>
            <?php
 include 'includes/header.php'; ?>
            <main class='content-area'>
                <div class='page-header' style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <h1>Consultation & Treatments</h1>
                        <p>Record and manage patient treatments and diagnoses.</p>
                    </div>
                    <button class="btn btn-primary" id="addTreatmentBtn"><i class="fa-solid fa-notes-medical"></i> New Consultation</button>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Dentist</th>
                                <th>Diagnosis</th>
                                <th>Procedure Done</th>
                                <th>Follow-up</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            try {
                                $stmt = $pdo->prepare("
                                    SELECT t.*, p.full_name 
                                    FROM treatments t 
                                    JOIN patients p ON t.patient_id = p.patient_id 
                                    WHERE t.clinic_id = ?
                                    ORDER BY t.created_at DESC
                                ");
                                $stmt->execute([$_SESSION['clinic_id']]);
                                $treatments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if(count($treatments) > 0) {
                                    foreach($treatments as $t) {
                                        echo "<tr>";
                                        echo "<td><strong>" . date('M d, Y', strtotime($t['created_at'])) . "</strong></td>";
                                        echo "<td><strong>" . htmlspecialchars($t['full_name']) . "</strong><br><span style='color:var(--text-muted);font-size:12px;'>" . htmlspecialchars($t['patient_id']) . "</span></td>";
                                        echo "<td>" . htmlspecialchars($t['dentist_name']) . "</td>";
                                        
                                        // Diagnosis (truncate if too long)
                                        $diag = htmlspecialchars($t['diagnosis']);
                                        if (strlen($diag) > 30) $diag = substr($diag, 0, 30) . '...';
                                        echo "<td>" . $diag . "</td>";
                                        
                                        $procedureHtml = "<span class='badge-tag'>" . htmlspecialchars($t['procedure_done']) . "</span>";
                                        if (!empty($t['materials_used'])) {
                                            $procedureHtml .= "<div style='font-size:11px; color:var(--text-muted); margin-top:4px;'><i class='fa-solid fa-box-open'></i> " . htmlspecialchars($t['materials_used']) . "</div>";
                                        }
                                        echo "<td>" . $procedureHtml . "</td>";
                                        
                                        $followUp = $t['follow_up_date'] ? date('M d, Y', strtotime($t['follow_up_date'])) : '<span style="color:#cbd5e1;">None</span>';
                                        echo "<td>" . $followUp . "</td>";
                                        
                                        echo "<td class='action-links'>
                                                <a href='patient_profile.php?id=" . urlencode($t['patient_id']) . "'><i class='fa-solid fa-user'></i> Profile</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center;'>No treatments recorded yet.</td></tr>";
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='7' style='text-align:center; color:red;'>Database Error: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </main>
        </div>
    </div>

    <!-- Add Treatment Modal -->
    <div class="modal-overlay" id="treatmentModal">
        <div class="modal-content" style="max-width: 650px;">
            <div class="modal-header">
                <h2>New Consultation / Treatment</h2>
                <button class="close-modal" id="closeTreatmentModalBtn">&times;</button>
            </div>
            <form id="addTreatmentForm">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Patient</label>
                            <select class="form-control" name="patient_id" required>
                                <option value="">-- Select Patient --</option>
                                <?php

                                $stmtPat = $pdo->prepare("SELECT patient_id, full_name FROM patients WHERE clinic_id = ? ORDER BY full_name ASC");
                                $stmtPat->execute([$_SESSION['clinic_id']]);
                                $patients = $stmtPat->fetchAll();
                                foreach($patients as $p) {
                                    echo "<option value='" . $p['patient_id'] . "'>" . htmlspecialchars($p['full_name']) . " (" . $p['patient_id'] . ")</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label>Dentist</label>
                            <?php
                            $isDentist = ($_SESSION['role'] === 'Dentist');
                            $currentUsername = $_SESSION['username'];
                            ?>
                            <select class="form-control" name="dentist_name" required <?php echo $isDentist ? 'readonly style="pointer-events:none; background:#f1f5f9;"' : ''; ?>>
                                <?php
                                if ($isDentist) {
                                    echo "<option value='" . htmlspecialchars($currentUsername) . "' selected>" . htmlspecialchars($currentUsername) . "</option>";
                                } else {
                                    echo "<option value=''>-- Select Dentist --</option>";
                                    $stmtDentists = $pdo->prepare("SELECT username FROM users WHERE role = 'Dentist' AND clinic_id = ? ORDER BY username ASC");
                                    $stmtDentists->execute([$_SESSION['clinic_id']]);
                                    $dentists = $stmtDentists->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($dentists as $d) {
                                        echo "<option value='" . htmlspecialchars($d['username']) . "'>" . htmlspecialchars($d['username']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Chief Complaint (What the patient feels)</label>
                            <input type="text" class="form-control" name="chief_complaint" placeholder="e.g. Toothache on lower right side for 3 days">
                        </div>

                        <div class="form-group full-width">
                            <label>Diagnosis</label>
                            <input type="text" class="form-control" name="diagnosis" placeholder="e.g. Dental caries on Tooth #30">
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Treatment Plan</label>
                            <textarea class="form-control" name="treatment_plan" placeholder="e.g. Recommend composite filling or possible root canal if deep"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Procedure Done Today</label>
                            <input list="procedureListTx" class="form-control" name="procedure_done" placeholder="Search or select procedure..." required>
                            <datalist id="procedureListTx">
                                <option value="Consultation">
                                <option value="Teeth Cleaning (Prophylaxis)">
                                <option value="Tooth Extraction">
                                <option value="Dental Fillings (Pasta)">
                                <option value="Root Canal Treatment">
                                <option value="Braces Installation">
                                <option value="Braces Adjustment">
                                <option value="Dental X-Ray">
                                <option value="Teeth Whitening">
                                <option value="Dentures">
                                <option value="Crown / Bridge">
                                <option value="Veneers">
                                <option value="Gum Treatment (Periodontics)">
                                <option value="Other">
                            </datalist>
                        </div>

                        <div class="form-group">
                            <label>Follow-up Date (Optional)</label>
                            <input type="date" class="form-control" name="follow_up_date">
                        </div>
                        
                        <!-- Inventory/Materials Used Section -->
                        <div class="form-group full-width">
                            <label>Materials Used from Inventory</label>
                            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
                                <div style="display:flex; gap:8px; margin-bottom:12px;">
                                    <select id="materialSelect" class="form-control" style="flex:1;">
                                        <option value="">-- Select Material --</option>
                                        <?php
                                        $stmtInv = $pdo->prepare("SELECT id, item_name, current_stock FROM inventory WHERE clinic_id = ? ORDER BY item_name ASC");
                                        $stmtInv->execute([$_SESSION['clinic_id']]);
                                        $invItems = $stmtInv->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($invItems as $inv) {
                                            $disabled = $inv['current_stock'] <= 0 ? 'disabled' : '';
                                            $stockText = $inv['current_stock'] > 0 ? " (Stock: {$inv['current_stock']})" : " (Out of Stock)";
                                            echo "<option value='{$inv['id']}' data-name='" . htmlspecialchars($inv['item_name']) . "' $disabled>" . htmlspecialchars($inv['item_name']) . $stockText . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <input type="number" id="materialQty" class="form-control" placeholder="Qty" min="1" style="width:80px;">
                                    <button type="button" id="addMaterialBtn" class="btn btn-outline"><i class="fa-solid fa-plus"></i> Add</button>
                                </div>
                                <table class="data-table" style="margin-top:0;">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th style="width:100px;">Quantity</th>
                                            <th style="width:50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="materialsTableBody">
                                        <!-- Dynamic items here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Additional Notes / Prescriptions</label>
                            <textarea class="form-control" name="notes" placeholder="e.g. Prescribed Amoxicillin 500mg every 8 hours for 7 days"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancelTreatmentModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Treatment Record</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/treatments.js"></script>
</body>
</html>

