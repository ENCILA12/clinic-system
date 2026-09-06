<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dental Clinic - Appointments</title>
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
                        <h1>Appointments</h1>
                        <p>Manage patient schedules and daily queue.</p>
                    </div>
                    <button class="btn btn-primary" id="addAptBtn"><i class="fa-solid fa-calendar-plus"></i> New Appointment</button>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Patient</th>
                                <th>Dentist</th>
                                <th>Procedure</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            try {
                                $stmt = $pdo->prepare("
                                    SELECT a.*, p.full_name 
                                    FROM appointments a 
                                    JOIN patients p ON a.patient_id = p.patient_id 
                                    WHERE a.clinic_id = ?
                                    ORDER BY a.appointment_date ASC, a.appointment_time ASC
                                ");
                                $stmt->execute([$_SESSION['clinic_id']]);
                                $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                $statuses = ['Pending', 'Confirmed', 'Arrived', 'Waiting', 'In Treatment', 'Completed', 'Cancelled', 'No Show'];

                                if(count($appointments) > 0) {
                                    foreach($appointments as $apt) {
                                        $statusClass = 'status-' . strtolower(str_replace(' ', '', $apt['status']));
                                        
                                        echo "<tr>";
                                        echo "<td><strong>" . date('M d, Y', strtotime($apt['appointment_date'])) . "</strong><br><span style='color:var(--text-muted);font-size:12px;'>" . date('h:i A', strtotime($apt['appointment_time'])) . "</span></td>";
                                        echo "<td><strong>" . htmlspecialchars($apt['full_name']) . "</strong><br><span style='color:var(--text-muted);font-size:12px;'>" . htmlspecialchars($apt['patient_id']) . "</span></td>";
                                        echo "<td>" . htmlspecialchars($apt['dentist_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($apt['procedure_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($apt['estimated_duration']) . "</td>";
                                        
                                        // Status Dropdown
                                        echo "<td>";
                                        echo "<span class='status-badge $statusClass'>";
                                        echo "<select class='status-dropdown' data-id='" . $apt['id'] . "'>";
                                        foreach($statuses as $s) {
                                            $selected = ($apt['status'] === $s) ? 'selected' : '';
                                            echo "<option value='$s' $selected>$s</option>";
                                        }
                                        echo "</select>";
                                        echo "</span>";
                                        echo "</td>";
                                        
                                        echo "<td class='action-links'>
                                                <a href='#'><i class='fa-solid fa-pen'></i> Edit</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center;'>No appointments scheduled yet.</td></tr>";
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

    <!-- Add Appointment Modal -->
    <div class="modal-overlay" id="aptModal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2>New Appointment</h2>
                <button class="close-modal" id="closeAptModalBtn">&times;</button>
            </div>
            <form id="addAptForm">
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
                        
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="appointment_date" required>
                        </div>
                        <div class="form-group">
                            <label>Time</label>
                            <input type="time" class="form-control" name="appointment_time" required>
                        </div>

                        <div class="form-group full-width">
                            <label>Dentist on Duty</label>
                            <select class="form-control" name="dentist_name" required>
                                <?php
                                $stmtDent = $pdo->prepare("SELECT username FROM users WHERE role = 'Dentist' AND clinic_id = ? ORDER BY username ASC");
                                $stmtDent->execute([$_SESSION['clinic_id']]);
                                $dents = $stmtDent->fetchAll(PDO::FETCH_ASSOC);
                                foreach($dents as $d) {
                                    echo "<option value='" . htmlspecialchars($d['username']) . "'>" . htmlspecialchars($d['username']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Procedure</label>
                            <input list="procedureList" class="form-control" name="procedure_name" placeholder="Search or select procedure..." required>
                            <datalist id="procedureList">
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
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label>Estimated Duration</label>
                            <select class="form-control" name="estimated_duration">
                                <option value="30 mins">30 mins</option>
                                <option value="1 hour">1 hour</option>
                                <option value="1.5 hours">1.5 hours</option>
                                <option value="2 hours">2 hours</option>
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Notes (Optional)</label>
                            <textarea class="form-control" name="notes" placeholder="Any special requests or concerns?"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancelAptModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Book Appointment</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/appointments.js"></script>
</body>
</html>

