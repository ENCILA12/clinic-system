<?php
require_once 'includes/auth.php'; // Ensure auth is checked first
require_once 'includes/db.php';

// Today's Date
$today = date('Y-m-d');
$currentMonth = date('Y-m');

$isDentist = ($_SESSION['role'] === 'Dentist');
$dentistName = $_SESSION['username']; // Using username as the identifier

try {
    // 1. Total Revenue Today
    if ($isDentist) {
        $stmtRevToday = $pdo->prepare("SELECT SUM(b.amount_paid) AS total FROM billing b JOIN treatments t ON b.patient_id = t.patient_id WHERE DATE(b.created_at) = ? AND t.dentist_name = ?");
        $stmtRevToday->execute([$today, $dentistName]);
    } else {
        $stmtRevToday = $pdo->prepare("SELECT SUM(amount_paid) AS total FROM billing WHERE DATE(created_at) = ?");
        $stmtRevToday->execute([$today]);
    }
    $revToday = $stmtRevToday->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total Revenue Month
    if ($isDentist) {
        $stmtRevMonth = $pdo->prepare("SELECT SUM(b.amount_paid) AS total FROM billing b JOIN treatments t ON b.patient_id = t.patient_id WHERE DATE_FORMAT(b.created_at, '%Y-%m') = ? AND t.dentist_name = ?");
        $stmtRevMonth->execute([$currentMonth, $dentistName]);
    } else {
        $stmtRevMonth = $pdo->prepare("SELECT SUM(amount_paid) AS total FROM billing WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
        $stmtRevMonth->execute([$currentMonth]);
    }
    $revMonth = $stmtRevMonth->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 2. Daily Patients (Appointments Today)
    if ($isDentist) {
        $stmtAppt = $pdo->prepare("SELECT COUNT(*) AS total, SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) AS completed FROM appointments WHERE appointment_date = ? AND dentist_name = ?");
        $stmtAppt->execute([$today, $dentistName]);
    } else {
        $stmtAppt = $pdo->prepare("SELECT COUNT(*) AS total, SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) AS completed FROM appointments WHERE appointment_date = ?");
        $stmtAppt->execute([$today]);
    }
    $apptStats = $stmtAppt->fetch(PDO::FETCH_ASSOC);
    $totalAppts = $apptStats['total'] ?? 0;
    $completedAppts = $apptStats['completed'] ?? 0;

    // 3. Completed Procedures Today (from treatments)
    if ($isDentist) {
        $stmtProc = $pdo->prepare("SELECT COUNT(*) AS total FROM treatments WHERE DATE(created_at) = ? AND dentist_name = ?");
        $stmtProc->execute([$today, $dentistName]);
    } else {
        $stmtProc = $pdo->prepare("SELECT COUNT(*) AS total FROM treatments WHERE DATE(created_at) = ?");
        $stmtProc->execute([$today]);
    }
    $procToday = $stmtProc->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 4. Total Outstanding Balances (Patient)
    if ($isDentist) {
        $stmtBal = $pdo->prepare("SELECT SUM(b.balance) AS total FROM billing b JOIN treatments t ON b.patient_id = t.patient_id WHERE b.balance > 0 AND b.payment_status != 'For HMO Claim' AND t.dentist_name = ?");
        $stmtBal->execute([$dentistName]);
        $outBalance = $stmtBal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        $stmtHMO = $pdo->prepare("SELECT SUM(b.balance) AS total FROM billing b JOIN treatments t ON b.patient_id = t.patient_id WHERE b.payment_status = 'For HMO Claim' AND t.dentist_name = ?");
        $stmtHMO->execute([$dentistName]);
        $hmoReceivables = $stmtHMO->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    } else {
        $stmtBal = $pdo->query("SELECT SUM(balance) AS total FROM billing WHERE balance > 0 AND payment_status != 'For HMO Claim'");
        $outBalance = $stmtBal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        $stmtHMO = $pdo->query("SELECT SUM(balance) AS total FROM billing WHERE payment_status = 'For HMO Claim'");
        $hmoReceivables = $stmtHMO->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    // 5. Most Performed Services (All time, top 5)
    $stmtTopServices = $pdo->query("SELECT service_name, SUM(quantity) as total_qty FROM billing_items GROUP BY service_name ORDER BY total_qty DESC LIMIT 5");
    $topServices = $stmtTopServices->fetchAll(PDO::FETCH_ASSOC);

    // 6. Inventory Alerts (Low Stock or Expired/Near Expiry)
    $stmtInv = $pdo->query("SELECT * FROM inventory WHERE current_stock <= minimum_stock OR (expiration_date IS NOT NULL AND expiration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)) ORDER BY expiration_date ASC LIMIT 5");
    $inventoryAlerts = $stmtInv->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Dashboard Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dental Clinic - Owner Dashboard</title>
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
                        <h1>Owner Dashboard</h1>
                        <p>Overview of clinic performance, revenue, and alerts for today (<?php
 echo date('F d, Y'); ?>).</p>
                    </div>
                </div>
                
                <!-- Stat Cards -->
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-icon revenue"><i class="fa-solid fa-peso-sign"></i></div>
                        <div class="stat-details">
                            <h3>Today's Revenue</h3>
                            <div class="stat-value">₱<?php
 echo number_format($revToday, 2); ?></div>
                            <div style="font-size:11px; color:gray; margin-top:4px;">This Month: ₱<?php
 echo number_format($revMonth, 2); ?></div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon patients"><i class="fa-solid fa-users"></i></div>
                        <div class="stat-details">
                            <h3>Daily Patients</h3>
                            <div class="stat-value"><?php
 echo $totalAppts; ?></div>
                            <div style="font-size:11px; color:gray; margin-top:4px;"><?php
 echo $completedAppts; ?> Completed</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon procedures"><i class="fa-solid fa-tooth"></i></div>
                        <div class="stat-details">
                            <h3>Procedures Today</h3>
                            <div class="stat-value"><?php
 echo $procToday; ?></div>
                            <div style="font-size:11px; color:gray; margin-top:4px;">Treatments logged today</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon balance"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                        <div class="stat-details">
                            <h3>Patient Balances</h3>
                            <div class="stat-value">₱<?php echo number_format($outBalance, 2); ?></div>
                            <div style="font-size:11px; color:gray; margin-top:4px;">Total unpaid by patients</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fa-solid fa-building-columns"></i></div>
                        <div class="stat-details">
                            <h3>HMO Receivables</h3>
                            <div class="stat-value">₱<?php echo number_format($hmoReceivables, 2); ?></div>
                            <div style="font-size:11px; color:gray; margin-top:4px;">Total to claim from HMO</div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-row">
                    <!-- Top Services Panel -->
                    <div class="dashboard-panel">
                        <div class="panel-title">
                            Most Performed Services <i class="fa-solid fa-chart-bar" style="color:var(--text-muted);"></i>
                        </div>
                        <div class="list-group">
                            <?php
 if (count($topServices) > 0): ?>
                                <?php
 foreach ($topServices as $ts): ?>
                                    <div class="list-group-item">
                                        <span style="font-weight:500;"><?php
 echo htmlspecialchars($ts['service_name']); ?></span>
                                        <span class="badge-tag" style="background:#e2e8f0; color:#475569;"><?php
 echo $ts['total_qty']; ?> times</span>
                                    </div>
                                <?php
 endforeach; ?>
                            <?php
 else: ?>
                                <div style="color:gray; font-size:14px; text-align:center; padding:20px;">No billing data available yet.</div>
                            <?php
 endif; ?>
                        </div>
                    </div>

                    <!-- Inventory Alerts Panel -->
                    <div class="dashboard-panel">
                        <div class="panel-title">
                            Inventory Alerts <a href="inventory.php" style="font-size:12px; font-weight:normal; color:var(--primary); text-decoration:none;">View All</a>
                        </div>
                        <div class="list-group">
                            <?php
 if (count($inventoryAlerts) > 0): ?>
                                <?php
 foreach ($inventoryAlerts as $inv): 
                                    $statusClass = 'alert-lowstock';
                                    $statusText = 'Low Stock';
                                    
                                    $isLowStock = ($inv['current_stock'] <= $inv['minimum_stock']);
                                    $isExpired = false;
                                    $isNearExpiry = false;

                                    if ($inv['expiration_date']) {
                                        $expDate = new DateTime($inv['expiration_date']);
                                        $todayDate = new DateTime();
                                        $interval = $todayDate->diff($expDate);
                                        $days = (int)$interval->format('%R%a');

                                        if ($days < 0) {
                                            $isExpired = true;
                                        } elseif ($days <= 30) {
                                            $isNearExpiry = true;
                                        }
                                    }

                                    if ($isExpired) {
                                        $statusClass = 'alert-expired';
                                        $statusText = 'Expired';
                                    } elseif ($isLowStock) {
                                        $statusClass = 'alert-lowstock';
                                        $statusText = 'Low Stock';
                                    } elseif ($isNearExpiry) {
                                        $statusClass = 'alert-nearexpiry';
                                        $statusText = 'Near Expiry';
                                    }
                                ?>
                                    <div class="list-group-item">
                                        <div>
                                            <div style="font-weight:500;"><?php
 echo htmlspecialchars($inv['item_name']); ?></div>
                                            <div style="font-size:12px; color:gray;">Stock: <?php
 echo $inv['current_stock']; ?> / Min: <?php
 echo $inv['minimum_stock']; ?></div>
                                        </div>
                                        <span class="badge-tag <?php
 echo $statusClass; ?>"><?php
 echo $statusText; ?></span>
                                    </div>
                                <?php
 endforeach; ?>
                            <?php
 else: ?>
                                <div style="color:gray; font-size:14px; text-align:center; padding:20px;">All inventory levels are good.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Calendar Row -->
                <div class="dashboard-row" style="margin-top:24px;">
                    <div class="dashboard-panel" style="width:100%;">
                        <div class="panel-title">
                            <i class="fa-regular fa-calendar-days"></i> Appointment Schedule
                        </div>
                        <div id="dashboardCalendar" style="min-height:500px;"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src="js/dashboard_calendar.js"></script>
</body>
</html>
