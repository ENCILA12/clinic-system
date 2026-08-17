<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Clinic Dashboard</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                            <p class="value">42</p>
                            <span class="trend positive"><i class="fa-solid fa-arrow-up"></i> 5%</span>
                        </div>
                    </div>
                    <div class="card summary-card">
                        <div class="card-icon teal"><i class="fa-solid fa-calendar-check"></i></div>
                        <div class="card-info">
                            <h3>Appointments Today</h3>
                            <p class="value">28</p>
                        </div>
                    </div>
                    <div class="card summary-card">
                        <div class="card-icon orange"><i class="fa-solid fa-hourglass-half"></i></div>
                        <div class="card-info">
                            <h3>Waiting Patients</h3>
                            <p class="value">5</p>
                        </div>
                    </div>
                    <div class="card summary-card">
                        <div class="card-icon purple"><i class="fa-solid fa-tooth"></i></div>
                        <div class="card-info">
                            <h3>Ongoing Treatment</h3>
                            <p class="value">2</p>
                        </div>
                    </div>
                    <div class="card summary-card">
                        <div class="card-icon green"><i class="fa-solid fa-peso-sign"></i></div>
                        <div class="card-info">
                            <h3>Revenue Today</h3>
                            <p class="value">₱15,400</p>
                            <span class="trend positive"><i class="fa-solid fa-arrow-up"></i> 12%</span>
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
