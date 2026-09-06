<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'includes/db.php';
$clinic_id = $_SESSION['clinic_id'];

// Patients Today
$stmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE clinic_id = ? AND DATE(created_at) = CURDATE()");
$stmt->execute([$clinic_id]);
$patientsToday = $stmt->fetchColumn();

// Revenue Today (Total amount from billing)
$stmt = $pdo->prepare("SELECT SUM(total_amount) FROM billing WHERE clinic_id = ? AND DATE(created_at) = CURDATE()");
$stmt->execute([$clinic_id]);
$revenueToday = $stmt->fetchColumn() ?: 0;

// Monthly Sales
$stmt = $pdo->prepare("SELECT SUM(total_amount) FROM billing WHERE clinic_id = ? AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
$stmt->execute([$clinic_id]);
$monthlySales = $stmt->fetchColumn() ?: 0;

// Monthly Census (Top Medical Cases)
$stmt = $pdo->prepare("
    SELECT procedure_done, COUNT(*) as count 
    FROM treatments 
    WHERE clinic_id = ? AND procedure_done IS NOT NULL AND procedure_done != '' 
      AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) 
    GROUP BY procedure_done 
    ORDER BY count DESC 
    LIMIT 5
");
$stmt->execute([$clinic_id]);
$monthlyCensus = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DentaFlow - Dashboard</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Pass PHP array to Javascript for Chart
        window.monthlyCensusData = <?php echo json_encode($monthlyCensus); ?>;
        window.monthlySalesData = <?php echo json_encode($monthlySales); ?>;
    </script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Dashboard Content -->
            <main class="content-area">
                <div class="page-header">
                    <h1>Overview</h1>
                    <p>Welcome back, here's today's clinic summary.</p>
                </div>

                <!-- Summary Cards -->
                <div class="summary-grid">
                    <div class="card summary-card">
                        <div class="card-icon blue"><i class="fa-solid fa-users"></i></div>
                        <div class="card-info">
                            <h3>Patients Today</h3>
                            <p class="value"><?php echo $patientsToday; ?></p>
                        </div>
                    </div>
                    <div class="card summary-card">
                        <div class="card-icon teal"><i class="fa-solid fa-calendar-check"></i></div>
                        <div class="card-info">
                            <h3>Monthly Sales</h3>
                            <p class="value">₱<?php echo number_format($monthlySales, 2); ?></p>
                        </div>
                    </div>
                    <div class="card summary-card">
                        <div class="card-icon orange"><i class="fa-solid fa-hourglass-half"></i></div>
                        <div class="card-info">
                            <h3>Waiting Patients</h3>
                            <p class="value">0</p>
                        </div>
                    </div>
                    <div class="card summary-card">
                        <div class="card-icon purple"><i class="fa-solid fa-tooth"></i></div>
                        <div class="card-info">
                            <h3>Ongoing Treatment</h3>
                            <p class="value">0</p>
                        </div>
                    </div>
                    <div class="card summary-card">
                        <div class="card-icon green"><i class="fa-solid fa-peso-sign"></i></div>
                        <div class="card-info">
                            <h3>Revenue Today</h3>
                            <p class="value">₱<?php echo number_format($revenueToday, 2); ?></p>
                        </div>
                    </div>
                    <div class="card summary-card">
                        <div class="card-icon red"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                        <div class="card-info">
                            <h3>Pending Payments</h3>
                            <p class="value">₱3,200</p>
                        </div>
                    </div>
                    <div class="card summary-card">
                        <div class="card-icon indigo"><i class="fa-solid fa-user-doctor"></i></div>
                        <div class="card-info">
                            <h3>Dentist on Duty</h3>
                            <p class="value">Dr. Reyes</p>
                        </div>
                    </div>
                </div>

                <!-- Charts Area -->
                <div class="charts-grid">
                    <div class="card chart-card">
                        <div class="card-header">
                            <h2>Appointments This Week</h2>
                        </div>
                        <div class="chart-container">
                            <canvas id="appointmentsChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="card chart-card">
                        <div class="card-header">
                            <h2>Monthly Revenue</h2>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <div class="card chart-card">
                        <div class="card-header">
                            <h2>Most Common Procedures</h2>
                        </div>
                        <div class="chart-container">
                            <canvas id="proceduresChart"></canvas>
                        </div>
                    </div>

                    <div class="card chart-card">
                        <div class="card-header">
                            <h2>New Patients per Month</h2>
                        </div>
                        <div class="chart-container">
                            <canvas id="newPatientsChart"></canvas>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Custom JS -->
    <script src="js/dashboard.js"></script>
</body>
</html>
