<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dental Clinic - Dentists</title>
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
                        <h1>Dentist Management</h1>
                        <p>Manage clinic doctors, their schedules, and consultation fees.</p>
                    </div>
                    <button class="btn btn-primary" id="addDentistBtn"><i class="fa-solid fa-user-doctor"></i> Add New Dentist</button>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Specialization</th>
                                <th>PRC License</th>
                                <th>Schedule</th>
                                <th>Consultation Fee</th>
                                <th>Availability</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            try {
                                $stmt = $pdo->prepare("SELECT * FROM dentists WHERE clinic_id = ? ORDER BY full_name ASC");
                                $stmt->execute([$_SESSION['clinic_id']]);
                                $dentists = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if(count($dentists) > 0) {
                                    foreach($dentists as $d) {
                                        echo "<tr>";
                                        echo "<td><strong>" . htmlspecialchars($d['full_name']) . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($d['specialization']) . "</td>";
                                        echo "<td>" . htmlspecialchars($d['prc_license']) . "</td>";
                                        $scheduleDisplay = htmlspecialchars($d['schedule']);
                                        $parsedSched = json_decode($d['schedule'], true);
                                        if (is_array($parsedSched) && isset($parsedSched['days'])) {
                                            $dayNames = ['0'=>'Sun', '1'=>'Mon', '2'=>'Tue', '3'=>'Wed', '4'=>'Thu', '5'=>'Fri', '6'=>'Sat'];
                                            $daysStr = implode(', ', array_map(function($day) use ($dayNames) { return $dayNames[$day] ?? ''; }, $parsedSched['days']));
                                            $scheduleDisplay = "<span style='font-weight:500;'>$daysStr</span><br><span style='font-size:12px; color:gray;'>" . date("h:i A", strtotime($parsedSched['start'])) . " - " . date("h:i A", strtotime($parsedSched['end'])) . "</span>";
                                        }

                                        echo "<td>" . $scheduleDisplay . "</td>";
                                        echo "<td>₱" . number_format($d['consultation_fee'], 2) . "</td>";
                                        
                                        if ($d['is_available']) {
                                            echo "<td><span class='badge-tag status-paid'>Available</span></td>";
                                        } else {
                                            echo "<td><span class='badge-tag status-unpaid'>Not Available</span></td>";
                                        }
                                        
                                        echo "<td class='action-links'>
                                                <a href='#' class='edit-dentist-btn' 
                                                    data-id='" . $d['id'] . "'
                                                    data-name='" . htmlspecialchars($d['full_name']) . "'
                                                    data-spec='" . htmlspecialchars($d['specialization']) . "'
                                                    data-prc='" . htmlspecialchars($d['prc_license']) . "'
                                                    data-sched='" . htmlspecialchars($d['schedule']) . "'
                                                    data-fee='" . $d['consultation_fee'] . "'
                                                    data-avail='" . $d['is_available'] . "'
                                                ><i class='fa-solid fa-pen'></i> Edit</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align:center;'>No dentists found.</td></tr>";
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

    <!-- Add/Edit Dentist Modal -->
    <div class="modal-overlay" id="dentistModal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Dentist</h2>
                <button class="close-modal" id="closeDentistModalBtn">&times;</button>
            </div>
            <form id="dentistForm">
                <input type="hidden" name="id" id="dentistId">
                <div class="modal-body">
                    <div class="form-group full-width">
                        <label>Full Name (Select from Users)</label>
                        <select class="form-control" name="full_name" id="dentistName" required>
                            <option value="">-- Select Dentist --</option>
                            <?php
                            $stmt = $pdo->prepare("SELECT full_name FROM users WHERE role = 'Dentist' AND clinic_id = ? ORDER BY full_name ASC");
                            $stmt->execute([$_SESSION['clinic_id']]);
                            $usersList = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach($usersList as $u) {
                                if(!empty($u['full_name'])) {
                                    echo "<option value='" . htmlspecialchars($u['full_name']) . "'>" . htmlspecialchars($u['full_name']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label>Specialization</label>
                        <input type="text" class="form-control" name="specialization" id="dentistSpec" placeholder="e.g. General Dentistry" required>
                    </div>

                    <div class="form-group full-width">
                        <label>PRC License Number</label>
                        <input type="text" class="form-control" name="prc_license" id="dentistPrc" required>
                    </div>

                    <input type="hidden" name="schedule" id="dentistSched">
                    
                    <div class="form-group full-width">
                        <label>Working Days</label>
                        <div style="display:flex; gap:10px; flex-wrap:wrap;" id="dentistDays">
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" class="sched-day" value="1"> Mon</label>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" class="sched-day" value="2"> Tue</label>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" class="sched-day" value="3"> Wed</label>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" class="sched-day" value="4"> Thu</label>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" class="sched-day" value="5"> Fri</label>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" class="sched-day" value="6"> Sat</label>
                            <label style="cursor:pointer; display:flex; align-items:center; gap:4px;"><input type="checkbox" class="sched-day" value="0"> Sun</label>
                        </div>
                    </div>

                    <div class="form-group full-width" style="display:flex; gap:16px;">
                        <div style="flex:1;">
                            <label>Start Time</label>
                            <input type="time" class="form-control" id="dentistStartTime" required>
                        </div>
                        <div style="flex:1;">
                            <label>End Time</label>
                            <input type="time" class="form-control" id="dentistEndTime" required>
                        </div>
                    </div>

                    <div style="display:flex; gap:16px;">
                        <div class="form-group" style="flex:1;">
                            <label>Consultation Fee (₱)</label>
                            <input type="number" class="form-control" name="consultation_fee" id="dentistFee" min="0" step="0.01" value="500.00" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label>Availability</label>
                            <select class="form-control" name="is_available" id="dentistAvail">
                                <option value="1">Available</option>
                                <option value="0">Not Available</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancelDentistModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveDentistBtn">Save Dentist</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/dentists.js"></script>
</body>
</html>

