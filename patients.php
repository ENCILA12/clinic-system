<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dental Clinic - Patients</title>
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
                        <h1>Patients</h1>
                        <p>Manage patient records and medical history.</p>
                    </div>
                    <button class="btn btn-primary" id="addPatientBtn"><i class="fa-solid fa-plus"></i> Add New Patient</button>
                </div>
                
                <!-- Table Container -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>Full Name</th>
                                <th>Contact Number</th>
                                <th>Age</th>
                                <th>Last Visit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            require_once 'includes/db.php';
                            try {
                                $stmt = $pdo->query("SELECT * FROM patients ORDER BY created_at DESC");
                                $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if(count($patients) > 0) {
                                    foreach($patients as $p) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($p['patient_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($p['full_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($p['contact_number']) . "</td>";
                                        echo "<td>" . htmlspecialchars($p['age']) . "</td>";
                                        echo "<td>" . date('M d, Y', strtotime($p['created_at'])) . "</td>";
                                        echo "<td class='action-links'>
                                                <a href='patient_profile.php?id=" . urlencode($p['patient_id']) . "'><i class='fa-solid fa-eye'></i> View</a>
                                                <a href='patient_profile.php?id=" . urlencode($p['patient_id']) . "&action=edit'><i class='fa-solid fa-pen'></i> Edit</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' style='text-align:center;'>No patients found. Click 'Add New Patient' to start.</td></tr>";
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='6' style='text-align:center; color:red;'>Database Error: " . $e->getMessage() . "</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </main>
        </div>
    </div>

    <!-- Add Patient Modal -->
    <div class="modal-overlay" id="patientModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Patient</h2>
                <button class="close-modal" id="closeModalBtn">&times;</button>
            </div>
            <form id="addPatientForm">
                <div class="modal-body">
                    <h3 class="form-section-title"><i class="fa-regular fa-id-card"></i> Basic Information</h3>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Full Name</label>
                            <input type="text" class="form-control" name="full_name" placeholder="e.g. Juan Dela Cruz" required>
                        </div>
                        <div class="form-group">
                            <label>Birthday</label>
                            <input type="date" class="form-control" id="bdayInput" name="birthday" required>
                        </div>
                        <div class="form-group">
                            <label>Age</label>
                            <input type="number" class="form-control" id="ageInput" name="age" readonly placeholder="Auto-calculated">
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select class="form-control" name="gender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" placeholder="09XX-XXX-XXXX">
                        </div>
                        <div class="form-group full-width">
                            <label>Email Address</label>
                            <input type="email" class="form-control" name="email" placeholder="email@example.com">
                        </div>
                        <div class="form-group full-width">
                            <label>Address</label>
                            <textarea class="form-control" name="address" placeholder="Complete Home Address"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Emergency Contact</label>
                            <input type="text" class="form-control" name="emergency_contact" placeholder="Name & Number">
                        </div>
                        <div class="form-group">
                            <label>Occupation</label>
                            <input type="text" class="form-control" name="occupation" placeholder="e.g. Teacher, Engineer">
                        </div>
                    </div>

                    <h3 class="form-section-title"><i class="fa-solid fa-notes-medical"></i> Medical & Dental Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Blood Type</label>
                            <select class="form-control" name="blood_type">
                                <option value="Unknown">Unknown</option>
                                <option value="A+">A+</option><option value="A-">A-</option>
                                <option value="B+">B+</option><option value="B-">B-</option>
                                <option value="O+">O+</option><option value="O-">O-</option>
                                <option value="AB+">AB+</option><option value="AB-">AB-</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Smoking Status</label>
                            <select class="form-control" name="smoking_status">
                                <option value="Non-Smoker">Non-Smoker</option>
                                <option value="Occasional">Occasional</option>
                                <option value="Heavy Smoker">Heavy Smoker</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label>Allergies (Medicines, Food, Latex, etc.)</label>
                            <input type="text" class="form-control" name="allergies" placeholder="Separate with commas">
                        </div>
                        <div class="form-group full-width">
                            <label>Medical Conditions (Diabetes, Heart Disease, etc.)</label>
                            <textarea class="form-control" name="medical_conditions" placeholder="List any existing medical conditions"></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label>Current Medications</label>
                            <textarea class="form-control" name="current_medications" placeholder="List any medications currently taking"></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label>Pregnancy (For Female Patients)</label>
                            <select class="form-control" name="pregnancy">
                                <option value="Not Applicable / No">Not Applicable / No</option>
                                <option value="Yes">Yes</option>
                                <option value="Trying to conceive">Trying to conceive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Patient Record</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/patients.js"></script>
</body>
</html>

