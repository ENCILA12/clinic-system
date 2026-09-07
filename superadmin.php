<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Superadmin') {
    header("Location: login.php");
    exit;
}
require_once 'includes/db.php';

// Handle Actions (Suspend, Activate, Reset, Edit Name)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_name') {
    $cid = $_POST['clinic_id'];
    $newName = trim($_POST['new_name']);
    $pdo->prepare("UPDATE clinics SET name = ? WHERE id = ?")->execute([$newName, $cid]);
    $msg = "Clinic name updated successfully.";
    header("Location: superadmin.php?msg=" . urlencode($msg));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_billing') {
    $cid = $_POST['clinic_id'];
    $price = $_POST['price'];
    $expiry = $_POST['expiry'];
    $pdo->prepare("UPDATE clinics SET subscription_price = ?, subscription_expiry = ? WHERE id = ?")->execute([$price, $expiry, $cid]);
    $msg = "Billing information updated successfully.";
    header("Location: superadmin.php?msg=" . urlencode($msg));
    exit;
}

if (isset($_GET['action']) && isset($_GET['clinic_id'])) {
    $action = $_GET['action'];
    $cid = $_GET['clinic_id'];
    
    if ($action === 'suspend') {
        $pdo->prepare("UPDATE clinics SET subscription_status = 'Suspended' WHERE id = ?")->execute([$cid]);
    } elseif ($action === 'activate') {
        $pdo->prepare("UPDATE clinics SET subscription_status = 'Active' WHERE id = ?")->execute([$cid]);
    } elseif ($action === 'reset_password') {
        $newPass = password_hash('password123', PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE clinic_id = ? AND role = 'Admin'")->execute([$newPass, $cid]);
        $msg = "Password reset to 'password123' for Clinic Admin.";
    }
    header("Location: superadmin.php?msg=" . urlencode($msg ?? 'Status updated successfully.'));
    exit;
}

// Fetch all clinics
$stmt = $pdo->query("
    SELECT c.*, 
           u.username as admin_username, u.email as admin_email,
           (SELECT COUNT(*) FROM patients WHERE clinic_id = c.id) as patient_count,
           (SELECT COUNT(*) FROM treatments WHERE clinic_id = c.id) as treatment_count
    FROM clinics c
    LEFT JOIN users u ON c.id = u.clinic_id AND u.role = 'Admin'
    ORDER BY c.id DESC
");
$clinics = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin - DentaFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .superadmin-header {
            background-color: #0f172a;
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .superadmin-content {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .panel-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
        }
        .register-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-Active { background: #dcfce7; color: #166534; }
        .status-Suspended { background: #fee2e2; color: #991b1b; }
        .status-Expired { background: #fef08a; color: #854d0e; }
        .action-btn {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            color: white;
            display: inline-block;
            margin-right: 5px;
        }
        .btn-suspend { background-color: #ef4444; }
        .btn-activate { background-color: #22c55e; }
        .btn-reset { background-color: #3b82f6; }
    </style>
</head>
<body style="background-color: #f8fafc; margin: 0;">

    <div class="superadmin-header">
        <div>
            <h2><i class="fa-solid fa-server"></i> DentaFlow Superadmin</h2>
            <p style="color: #94a3b8; font-size: 14px; margin:0;">Manage your subscribers and revenue</p>
        </div>
        <a href="logout.php" style="color: #ef4444; text-decoration: none; font-weight: 600;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>

    <div class="superadmin-content">
        <?php if(isset($_GET['msg'])): ?>
            <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($_GET['msg']); ?>
            </div>
        <?php endif; ?>

        <?php
        $mrr = 0;
        $active_clinics = 0;
        $total_kb = 0;
        foreach($clinics as $c) {
            if ($c['subscription_status'] === 'Active') {
                $mrr += $c['subscription_price'];
                $active_clinics++;
            }
            $total_kb += ($c['patient_count'] + $c['treatment_count']) * 5;
        }
        ?>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 4px solid #22c55e;">
                <div style="color: #64748b; font-size: 14px; font-weight: 600;">Monthly Recurring Revenue</div>
                <div style="font-size: 28px; font-weight: 700; color: #0f172a; margin-top: 5px;">₱<?php echo number_format($mrr, 2); ?></div>
            </div>
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 4px solid #3b82f6;">
                <div style="color: #64748b; font-size: 14px; font-weight: 600;">Active Clinics</div>
                <div style="font-size: 28px; font-weight: 700; color: #0f172a; margin-top: 5px;"><?php echo $active_clinics; ?></div>
            </div>
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 4px solid #8b5cf6;">
                <div style="color: #64748b; font-size: 14px; font-weight: 600;">Total Storage Used</div>
                <div style="font-size: 28px; font-weight: 700; color: #0f172a; margin-top: 5px;"><?php echo ($total_kb > 1024) ? round($total_kb/1024, 2) . ' MB' : $total_kb . ' KB'; ?></div>
            </div>
        </div>

        <div class="panel-grid">
            <!-- Add New Clinic Form -->
            <div class="register-card">
                <h3><i class="fa-solid fa-plus-circle"></i> Register New Clinic</h3>
                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #e2e8f0;">
                <form action="api/add_clinic.php" method="POST">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Clinic Name</label>
                        <input type="text" name="clinic_name" class="form-control" required>
                    </div>
                    
                    <h4 style="margin: 20px 0 10px 0;">Admin Account</h4>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Admin Username</label>
                        <input type="text" name="admin_username" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Admin Email</label>
                        <input type="email" name="admin_email" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Admin Password</label>
                        <input type="password" name="admin_password" class="form-control" required>
                    </div>
                    
                    <h4 style="margin: 20px 0 10px 0;">Billing / Subscription</h4>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Monthly/Yearly Price (₱)</label>
                        <input type="number" step="0.01" name="subscription_price" class="form-control" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Expiry Date</label>
                        <input type="date" name="subscription_expiry" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Create Subscriber Account</button>
                </form>
            </div>

            <!-- List of Clinics -->
            <div class="register-card">
                <h3><i class="fa-solid fa-building"></i> Registered Clinics</h3>
                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #e2e8f0;">
                
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                    <thead>
                        <tr style="background: #f1f5f9;">
                            <th style="padding: 12px; border-bottom: 1px solid #e2e8f0;">ID</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Clinic Name</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Admin Account</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Billing Info</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Est. Storage</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Status</th>
                            <th style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clinics as $c): 
                            // Estimate storage (Very basic approximation: 5KB per record)
                            $totalRecords = $c['patient_count'] + $c['treatment_count'];
                            $estKb = $totalRecords * 5;
                            $storageStr = $estKb > 1024 ? round($estKb/1024, 2) . ' MB' : $estKb . ' KB';
                        ?>
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 12px;">#<?php echo $c['id']; ?></td>
                            <td style="padding: 12px;">
                                <div style="font-weight: 600; font-size: 15px; color: #0f172a;"><?php echo htmlspecialchars($c['name']); ?></div>
                                <?php if (!empty($c['slug'])): ?>
                                <div style="margin-top: 4px; display: flex; align-items: center; gap: 6px;">
                                    <input type="text" readonly value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/CLINIC system/' . $c['slug'] . '/login.php'; ?>" style="font-size: 11px; padding: 2px 6px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f8fafc; color: #64748b; width: 220px; cursor: text;" onclick="this.select();">
                                    <a href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/CLINIC system/' . $c['slug'] . '/login.php'; ?>" target="_blank" style="color: #3b82f6; font-size: 12px; text-decoration: none;" title="Open in new tab"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                </div>
                                <?php else: ?>
                                <span style="font-size: 11px; color: #ef4444;">No slug generated</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px;">
                                <?php echo htmlspecialchars($c['admin_username']); ?><br>
                                <span style="color: gray; font-size: 12px;"><?php echo htmlspecialchars($c['admin_email']); ?></span>
                            </td>
                            <td style="padding: 12px;">
                                ₱<?php echo number_format($c['subscription_price'], 2); ?><br>
                                <span style="color: gray; font-size: 12px;">Exp: <?php echo $c['subscription_expiry'] ? date('M d, Y', strtotime($c['subscription_expiry'])) : 'N/A'; ?></span>
                            </td>
                            <td style="padding: 12px;"><?php echo $storageStr; ?></td>
                            <td style="padding: 12px;">
                                <span class="status-badge status-<?php echo $c['subscription_status']; ?>"><?php echo $c['subscription_status']; ?></span>
                            </td>
                            <td style="padding: 12px;">
                                <?php if($c['subscription_status'] === 'Active'): ?>
                                    <a href="superadmin.php?action=suspend&clinic_id=<?php echo $c['id']; ?>" class="action-btn btn-suspend" onclick="return confirm('Suspend this clinic?');">Suspend</a>
                                <?php else: ?>
                                    <a href="superadmin.php?action=activate&clinic_id=<?php echo $c['id']; ?>" class="action-btn btn-activate">Activate</a>
                                <?php endif; ?>
                                <a href="superadmin.php?action=reset_password&clinic_id=<?php echo $c['id']; ?>" class="action-btn btn-reset" onclick="return confirm('Reset admin password to password123?');" style="margin-top:5px;">Reset Pass</a>
                                <a href="#" class="action-btn" onclick="editClinicName(<?php echo $c['id']; ?>, '<?php echo addslashes(htmlspecialchars($c['name'])); ?>'); return false;" style="background-color: #f59e0b; margin-top:5px;">Edit Name</a>
                                <a href="#" class="action-btn" onclick="editBilling(<?php echo $c['id']; ?>, '<?php echo $c['subscription_price']; ?>', '<?php echo $c['subscription_expiry']; ?>'); return false;" style="background-color: #8b5cf6; margin-top:5px;">Manage Billing</a>
                                <a href="api/login_as.php?clinic_id=<?php echo $c['id']; ?>" class="action-btn" style="background-color: #1e293b; margin-top:5px;" onclick="return confirm('Log in as Admin for this clinic?');"><i class="fa-solid fa-right-to-bracket"></i> Login As</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
    function editClinicName(id, currentName) {
        let newName = prompt("Enter new clinic name:", currentName);
        if (newName !== null && newName.trim() !== "" && newName !== currentName) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = 'superadmin.php';
            
            let actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'edit_name';
            form.appendChild(actionInput);

            let idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'clinic_id';
            idInput.value = id;
            form.appendChild(idInput);

            let nameInput = document.createElement('input');
            nameInput.type = 'hidden';
            nameInput.name = 'new_name';
            nameInput.value = newName.trim();
            form.appendChild(nameInput);

            document.body.appendChild(form);
            form.submit();
        }
    }

    function editBilling(id, currentPrice, currentExpiry) {
        let newPrice = prompt("Enter new monthly/yearly price (₱):", currentPrice);
        if (newPrice !== null && newPrice.trim() !== "") {
            let newExpiry = prompt("Enter new expiry date (YYYY-MM-DD):", currentExpiry);
            if (newExpiry !== null && newExpiry.trim() !== "") {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = 'superadmin.php';
                
                let actionInput = document.createElement('input'); actionInput.type = 'hidden'; actionInput.name = 'action'; actionInput.value = 'edit_billing'; form.appendChild(actionInput);
                let idInput = document.createElement('input'); idInput.type = 'hidden'; idInput.name = 'clinic_id'; idInput.value = id; form.appendChild(idInput);
                let priceInput = document.createElement('input'); priceInput.type = 'hidden'; priceInput.name = 'price'; priceInput.value = newPrice.trim(); form.appendChild(priceInput);
                let expiryInput = document.createElement('input'); expiryInput.type = 'hidden'; expiryInput.name = 'expiry'; expiryInput.value = newExpiry.trim(); form.appendChild(expiryInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
    }
    </script>
</body>
</html>
