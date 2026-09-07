<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dental Clinic - Billing & Payments</title>
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
                        <h1>Billing & Payments</h1>
                        <p>Manage invoices, compute totals, and track patient payments.</p>
                    </div>
                    <button class="btn btn-primary" id="addBillingBtn"><i class="fa-solid fa-file-invoice-dollar"></i> Create Invoice</button>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Invoice ID</th>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Total Amount</th>
                                <th>Balance</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th style="text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            try {
                                $stmt = $pdo->prepare("
                                    SELECT b.*, p.full_name 
                                    FROM billing b 
                                    JOIN patients p ON b.patient_id = p.patient_id 
                                    WHERE b.clinic_id = ?
                                    ORDER BY b.created_at DESC
                                ");
                                $stmt->execute([$_SESSION['clinic_id']]);
                                $bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if(count($bills) > 0) {
                                    foreach($bills as $b) {
                                        echo "<tr>";
                                        echo "<td><strong>" . htmlspecialchars($b['invoice_id']) . "</strong></td>";
                                        echo "<td>" . date('M d, Y', strtotime($b['created_at'])) . "</td>";
                                        echo "<td><strong>" . htmlspecialchars($b['full_name']) . "</strong></td>";
                                        echo "<td>₱" . number_format($b['total_amount'], 2) . "</td>";
                                        
                                        $balanceColor = $b['balance'] > 0 ? "color:#ef4444; font-weight:600;" : "color:var(--text-muted);";
                                        echo "<td style='$balanceColor'>₱" . number_format($b['balance'], 2) . "</td>";
                                        
                                        if ($b['payment_method'] === 'HMO') {
                                            echo "<td><strong>HMO</strong><br><span style='font-size:12px; color:var(--text-muted);'>" . htmlspecialchars($b['hmo_provider']) . "</span></td>";
                                        } else {
                                            echo "<td>" . htmlspecialchars($b['payment_method']) . "</td>";
                                        }
                                        
                                        $statusClass = 'status-' . strtolower(str_replace(' ', '-', $b['payment_status']));
                                        echo "<td><span class='badge-tag $statusClass'>" . htmlspecialchars($b['payment_status']) . "</span></td>";
                                        
                                        echo "<td style='text-align:center;'>";
                                        if ($b['balance'] > 0) {
                                            echo "<button class='btn btn-outline' style='padding:4px 8px; font-size:12px;' onclick='payBalance(\"" . htmlspecialchars($b['invoice_id']) . "\", " . floatval($b['balance']) . ")'>Pay Balance</button>";
                                        } else {
                                            echo "<span style='color:var(--text-muted); font-size:12px;'><i class='fa-solid fa-check'></i> Cleared</span>";
                                        }
                                        echo "</td>";
                                        
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center;'>No invoices found.</td></tr>";
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

    <!-- Create Invoice Modal -->
    <div class="modal-overlay" id="billingModal">
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h2>Create Invoice</h2>
                <button class="close-modal" id="closeBillingModalBtn">&times;</button>
            </div>
            <form id="addBillingForm">
                <div class="modal-body">
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

                    <h3 class="form-section-title"><i class="fa-solid fa-list-check"></i> Services Rendered</h3>
                    
                    <div style="display:flex; gap:12px; margin-bottom:16px;">
                        <input list="serviceList" class="form-control" id="serviceSelect" style="flex:2;" placeholder="Search or select service...">
                        <datalist id="serviceList">
                            <option value="Consultation" data-price="500">
                            <option value="Oral Prophylaxis (Cleaning)" data-price="1000">
                            <option value="Tooth Extraction (Simple)" data-price="800">
                            <option value="Tooth Extraction (Complicated)" data-price="1500">
                            <option value="Dental Fillings (Composite/Pasta)" data-price="1000">
                            <option value="Root Canal Treatment" data-price="5000">
                            <option value="Braces Installation" data-price="15000">
                            <option value="Braces Adjustment" data-price="1500">
                            <option value="Dental X-Ray (Panoramic)" data-price="1000">
                            <option value="Dental X-Ray (Periapical)" data-price="500">
                            <option value="Teeth Whitening" data-price="5000">
                            <option value="Crown (Porcelain)" data-price="6000">
                            <option value="Crown (Zirconia)" data-price="10000">
                            <option value="Bridge" data-price="15000">
                            <option value="Denture (Complete)" data-price="8000">
                            <option value="Denture (Partial)" data-price="5000">
                            <option value="Implant" data-price="30000">
                            <option value="Veneers" data-price="12000">
                            <option value="Gum Treatment (Periodontics)" data-price="3500">
                        </datalist>
                        <button type="button" class="btn btn-primary" id="addServiceBtn" style="white-space:nowrap;">Add Item</button>
                    </div>

                    <table class="invoice-items-table" id="invoiceItemsTable">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th style="width:80px;">Qty</th>
                                <th style="width:120px;">Price (₱)</th>
                                <th style="width:120px;">Amount (₱)</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItemsBody">
                            <!-- Items will be dynamically added here -->
                        </tbody>
                    </table>

                    <div class="invoice-summary">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>₱<span id="displaySubtotal">0.00</span></span>
                            <input type="hidden" name="subtotal" id="inputSubtotal" value="0">
                        </div>
                        <div class="summary-row" style="align-items:center;">
                            <span>Discount (₱):</span>
                            <input type="number" class="form-control" name="discount" id="inputDiscount" value="0" min="0" step="0.01" style="width:120px; text-align:right; padding:4px 8px;">
                        </div>
                        <div class="summary-row total">
                            <span>Grand Total:</span>
                            <span>₱<span id="displayTotal">0.00</span></span>
                            <input type="hidden" name="total_amount" id="inputTotal" value="0">
                        </div>
                        <div class="summary-row" style="align-items:center; margin-top:16px;">
                            <span>Payment Method:</span>
                            <select class="form-control" name="payment_method" id="paymentMethodSelect" style="width:120px; padding:4px 8px;">
                                <option value="Cash">Cash</option>
                                <option value="GCash">GCash</option>
                                <option value="Card">Card</option>
                                <option value="HMO">HMO</option>
                            </select>
                        </div>
                        
                        <!-- HMO Fields (Hidden by default) -->
                        <div id="hmoFields" style="display:none; background:#f8fafc; padding:12px; border-radius:8px; margin-top:12px; border:1px solid #cbd5e1;">
                            <div class="summary-row" style="align-items:center; margin-bottom:8px;">
                                <span style="font-size:13px;">HMO Provider:</span>
                                <select class="form-control" name="hmo_provider" style="width:140px; padding:4px 8px;">
                                    <option value="Maxicare">Maxicare</option>
                                    <option value="Intellicare">Intellicare</option>
                                    <option value="Medicard">Medicard</option>
                                    <option value="ValuCare">ValuCare</option>
                                    <option value="PhilHealth">PhilHealth</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="summary-row" style="align-items:center;">
                                <span style="font-size:13px;">Approval Code:</span>
                                <input type="text" class="form-control" name="hmo_approval_code" placeholder="e.g. MX-123456" style="width:140px; padding:4px 8px;">
                            </div>
                        </div>
                        <div class="summary-row" style="align-items:center;">
                            <span>Amount Paid (₱):</span>
                            <input type="number" class="form-control" name="amount_paid" id="inputPaid" value="0" min="0" step="0.01" style="width:120px; text-align:right; padding:4px 8px;" required>
                        </div>
                        <div class="summary-row">
                            <span>Balance:</span>
                            <span style="color:#ef4444;">₱<span id="displayBalance">0.00</span></span>
                            <input type="hidden" name="balance" id="inputBalance" value="0">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancelBillingModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveInvoiceBtn">Save Invoice</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/billing.js?v=2"></script>
</body>
</html>

