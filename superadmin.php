<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Superadmin') {
    header("Location: login.php");
    exit;
}
require_once 'includes/db.php';

// Handle Actions (Suspend, Activate, Reset)
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
                            <td style="padding: 12px; font-weight: 600;"><?php echo htmlspecialchars($c['name']); ?></td>
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
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
