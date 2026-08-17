<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: patients.php");
    exit;
}

$patient_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    echo "Patient not found.";
    exit;
}

$initials = strtoupper(substr($patient['full_name'], 0, 1));
$isEditAction = (isset($_GET['action']) && $_GET['action'] === 'edit');
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Patient Profile - <?php
 echo htmlspecialchars($patient['full_name']); ?></title>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <link rel='stylesheet' href='css/style.css'>
</head>
<body>
    <div class='dashboard-container'>
        <?php
 
        // Fake current page for sidebar highlight
        $_SERVER['PHP_SELF'] = 'patients.php';
        include 'includes/sidebar.php'; 
        ?>
        <div class='main-content'>
            <?php
 include 'includes/header.php'; ?>
            <main class='content-area'>
                
                <div class="page-header">
                    <a href="patients.php" style="color: var(--text-muted); text-decoration: none; margin-bottom: 12px; display: inline-block;">
                        <i class="fa-solid fa-arrow-left"></i> Back to Patients
                    </a>
                </div>

                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-info-main">
                        <div class="profile-avatar"><?php
 echo $initials; ?></div>
                        <div class="profile-details">
                            <h2><?php
 echo htmlspecialchars($patient['full_name']); ?></h2>
                            <div class="patient-id"><?php
 echo htmlspecialchars($patient['patient_id']); ?></div>
                            <div class="profile-meta">
                                <span><i class="fa-solid fa-cake-candles"></i> <?php
 echo htmlspecialchars($patient['age']); ?> years old</span>
                                <span><i class="fa-solid fa-phone"></i> <?php
 echo htmlspecialchars($patient['contact_number']); ?></span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" id="editPatientBtn"><i class="fa-solid fa-pen"></i> Edit Profile</button>
                </div>

                <!-- Info Cards -->
                <div class="profile-grid">
                    
                    <!-- Basic Info Card -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fa-regular fa-id-card"></i> Basic Information
                        </div>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Birthday</span>
                                <span class="info-value"><?php
 echo date('F d, Y', strtotime($patient['birthday'])); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Gender</span>
                                <span class="info-value"><?php
 echo htmlspecialchars($patient['gender']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email Address</span>
                                <span class="info-value"><?php
 echo htmlspecialchars($patient['email']) ?: '-'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Address</span>
                                <span class="info-value"><?php
 echo nl2br(htmlspecialchars($patient['address'])) ?: '-'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Emergency Contact</span>
                                <span class="info-value"><?php
 echo htmlspecialchars($patient['emergency_contact']) ?: '-'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Occupation</span>
                                <span class="info-value"><?php
 echo htmlspecialchars($patient['occupation']) ?: '-'; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Info Card -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fa-solid fa-notes-medical"></i> Medical & Dental Info
                        </div>
                        <div class="info-list">
                            <div class="info-item">
                                <span class="info-label">Blood Type</span>
                                <span class="info-value">
                                    <span class="badge-tag"><?php
 echo htmlspecialchars($patient['blood_type']) ?: 'Unknown'; ?></span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Smoking Status</span>
                                <span class="info-value"><?php
 echo htmlspecialchars($patient['smoking_status']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Allergies</span>
                                <span class="info-value">
                                    <?php
 if($patient['allergies']): ?>
                                        <span class="badge-tag warning"><i class="fa-solid fa-triangle-exclamation"></i> <?php
 echo htmlspecialchars($patient['allergies']); ?></span>
                                    <?php
 else: ?>
                                        None reported
                                    <?php
 endif; ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Medical Conditions</span>
                                <span class="info-value"><?php
 echo nl2br(htmlspecialchars($patient['medical_conditions'])) ?: 'None reported'; ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Current Medications</span>
                                <span class="info-value"><?php
 echo nl2br(htmlspecialchars($patient['current_medications'])) ?: 'None'; ?></span>
                            </div>
                            <?php
 if(strtolower($patient['gender']) === 'female'): ?>
                            <div class="info-item">
                                <span class="info-label">Pregnancy Status</span>
                                <span class="info-value"><?php
 echo htmlspecialchars($patient['pregnancy']) ?: 'Not Applicable / No'; ?></span>
                            </div>
                            <?php
 endif; ?>
                        </div>
                    </div>

                </div>

                <!-- Treatment History Timeline Section -->
                <div class="dental-chart-section" style="margin-bottom: 24px;">
                    <div class="dental-chart-header">
                        <i class="fa-solid fa-timeline"></i> Treatment History Timeline
                    </div>
                    <div class="timeline-container">
                        <?php

                        try {
                            // Fetch Consultations
                            $stmt1 = $pdo->prepare("SELECT created_at AS date, 'Consultation' AS type, diagnosis, procedure_done AS action, dentist_name FROM treatments WHERE patient_id = ?");
                            $stmt1->execute([$patient_id]);
                            $t_records = $stmt1->fetchAll(PDO::FETCH_ASSOC);

                            // Fetch Dental Chart Records
                            $stmt2 = $pdo->prepare("SELECT created_at AS date, 'Tooth Chart' AS type, diagnosis, CONCAT(treatment, ' on Tooth #', tooth_number) AS action, dentist_name FROM dental_records WHERE patient_id = ?");
                            $stmt2->execute([$patient_id]);
                            $d_records = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                            // Merge and Sort by Date (Descending)
                            $timeline = array_merge($t_records, $d_records);
                            usort($timeline, function($a, $b) {
                                return strtotime($b['date']) - strtotime($a['date']);
                            });

                            if(count($timeline) > 0) {
                                foreach($timeline as $item) {
                                    $dateStr = date('F d, Y', strtotime($item['date']));
                                    $title = htmlspecialchars($item['action'] ? $item['action'] : 'Checkup');
                                    $desc = htmlspecialchars($item['diagnosis']);
                                    $doctor = htmlspecialchars($item['dentist_name']);
                                    
                                    echo "
                                    <div class='timeline-item'>
                                        <div class='timeline-date'>$dateStr</div>
                                        <div class='timeline-content'>
                                            <div class='timeline-title'>$title</div>
                                            <div class='timeline-desc'>
                                                $desc <br>
                                                <small style='color:var(--primary);'><i class='fa-solid fa-user-doctor'></i> $doctor</small>
                                            </div>
                                        </div>
                                    </div>";
                                }
                            } else {
                                echo "<div style='color:gray; padding-left:16px;'>No treatments recorded yet.</div>";
                            }
                        } catch(PDOException $e) {
                            echo "<div style='color:red;'>Error loading timeline: " . $e->getMessage() . "</div>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Attachments Section -->
                <div class="dental-chart-section" style="margin-bottom: 24px;">
                    <div class="dental-chart-header" style="display:flex; justify-content:space-between; align-items:center;">
                        <div><i class="fa-solid fa-paperclip"></i> Files & Attachments</div>
                        <button class="btn btn-outline" style="padding:4px 12px; font-size:12px;" onclick="document.getElementById('attachmentFormWrapper').style.display='block'">+ Add File</button>
                    </div>
                    
                    <div id="attachmentFormWrapper" style="display:none; background:#f8fafc; padding:16px; border-radius:8px; margin-bottom:16px; border:1px solid var(--border-color);">
                        <form id="uploadAttachmentForm" enctype="multipart/form-data">
                            <input type="hidden" name="patient_id" value="<?php
 echo htmlspecialchars($patient_id); ?>">
                            <div style="display:flex; gap:16px; align-items:end;">
                                <div class="form-group" style="flex:1; margin-bottom:0;">
                                    <label>File Type</label>
                                    <select class="form-control" name="file_type" required>
                                        <option value="X-Ray">X-Ray</option>
                                        <option value="Intraoral Photo">Intraoral Photo</option>
                                        <option value="Consent Form">Consent Form</option>
                                        <option value="Lab Result">Lab Result</option>
                                        <option value="Before/After Photo">Before/After Photo</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group" style="flex:2; margin-bottom:0;">
                                    <label>Choose File</label>
                                    <input type="file" class="form-control" name="attachment_file" required>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <button type="submit" class="btn btn-primary" id="uploadBtn">Upload</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="attachments-grid">
                        <?php

                        try {
                            $stmtAttach = $pdo->prepare("SELECT * FROM attachments WHERE patient_id = ? ORDER BY uploaded_at DESC");
                            $stmtAttach->execute([$patient_id]);
                            $attachments = $stmtAttach->fetchAll(PDO::FETCH_ASSOC);

                            if(count($attachments) > 0) {
                                foreach($attachments as $att) {
                                    $path = htmlspecialchars($att['file_path']);
                                    $name = htmlspecialchars($att['file_name']);
                                    $type = htmlspecialchars($att['file_type']);
                                    $date = date('M d, Y', strtotime($att['uploaded_at']));
                                    
                                    // Check if image
                                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                                    $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                    
                                    echo "<div class='attachment-card'>";
                                    echo "<a href='$path' target='_blank' style='text-decoration:none; color:inherit;'>";
                                    echo "<div class='attachment-preview'>";
                                    if ($isImg) {
                                        echo "<img src='$path' alt='$name' loading='lazy'>";
                                    } else {
                                        echo "<i class='fa-solid fa-file-pdf'></i>";
                                    }
                                    echo "</div>";
                                    echo "<div class='attachment-info'>";
                                    echo "<div class='type-badge'>$type</div>";
                                    echo "<div style='font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;' title='$name'>$name</div>";
                                    echo "<div style='color:gray;'>$date</div>";
                                    echo "</div>";
                                    echo "</a>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<div style='color:gray; grid-column: 1 / -1;'>No attachments uploaded yet.</div>";
                            }
                        } catch(PDOException $e) {
                            echo "<div style='color:red;'>Error loading attachments.</div>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Visual Dental Chart Section -->
                <div class="dental-chart-section">
                    <div class="dental-chart-header">
                        <i class="fa-solid fa-tooth"></i> Interactive Dental Chart
                    </div>
                    
                    <?php

                    // Fetch latest status for all teeth for this patient
                    $chartStmt = $pdo->prepare("SELECT tooth_number, status FROM dental_records WHERE patient_id = ? ORDER BY created_at ASC");
                    $chartStmt->execute([$patient_id]);
                    $records = $chartStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $toothStatuses = [];
                    foreach($records as $r) {
                        // The last one will overwrite earlier ones, giving us the latest status
                        $toothStatuses[$r['tooth_number']] = $r['status'];
                    }
                    
                    function renderTooth($num, $statuses) {
                        $status = isset($statuses[$num]) ? $statuses[$num] : 'Healthy';
                        $class = 'status-' . strtolower(str_replace(' ', '', $status));
                        if($status === 'Healthy') $class = '';
                        echo "<div class='tooth $class' data-tooth='$num' title='Tooth $num: $status'>$num</div>";
                    }
                    ?>

                    <!-- Upper Arch (1-16) -->
                    <div class="arch-label">Upper Arch</div>
                    <div class="arch-container arch-upper">
                        <?php
 for($i=1; $i<=16; $i++) renderTooth($i, $toothStatuses); ?>
                    </div>
                    
                    <!-- Lower Arch (17-32) -->
                    <div class="arch-label">Lower Arch</div>
                    <div class="arch-container arch-lower">
                        <?php
 for($i=32; $i>=17; $i--) renderTooth($i, $toothStatuses); ?>
                    </div>

                    <!-- Legend -->
                    <div class="chart-legend">
                        <div class="legend-item"><div class="legend-box" style="background:#fff; border-color:#cbd5e1;"></div> Healthy</div>
                        <div class="legend-item"><div class="legend-box" style="background:#fee2e2; border-color:#ef4444;"></div> Cavity</div>
                        <div class="legend-item"><div class="legend-box" style="background:#e0f2fe; border-color:#0ea5e9;"></div> Filling</div>
                        <div class="legend-item"><div class="legend-box" style="background:#f3e8ff; border-color:#a855f7;"></div> Root Canal</div>
                        <div class="legend-item"><div class="legend-box" style="background:#fef08a; border-color:#eab308;"></div> Crown</div>
                        <div class="legend-item"><div class="legend-box" style="background:#f1f5f9; border-color:#94a3b8;"></div> Missing</div>
                        <div class="legend-item"><div class="legend-box" style="background:#1e293b; border-color:#0f172a;"></div> Extracted</div>
                        <div class="legend-item"><div class="legend-box" style="background:#ccfbf1; border-color:#14b8a6;"></div> Implant</div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div class="modal-overlay <?php
 echo $isEditAction ? 'active' : ''; ?>" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Patient Profile</h2>
                <button class="close-modal" id="closeEditModalBtn">&times;</button>
            </div>
            <form id="editPatientForm">
                <div class="modal-body">
                    <input type="hidden" name="patient_id" value="<?php
 echo htmlspecialchars($patient['patient_id']); ?>">
                    
                    <h3 class="form-section-title"><i class="fa-regular fa-id-card"></i> Basic Information</h3>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label>Full Name</label>
                            <input type="text" class="form-control" name="full_name" value="<?php
 echo htmlspecialchars($patient['full_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Birthday</label>
                            <input type="date" class="form-control" id="editBdayInput" name="birthday" value="<?php
 echo htmlspecialchars($patient['birthday']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Age</label>
                            <input type="number" class="form-control" id="editAgeInput" name="age" value="<?php
 echo htmlspecialchars($patient['age']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select class="form-control" name="gender">
                                <option value="Male" <?php
 echo $patient['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php
 echo $patient['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php
 echo $patient['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" value="<?php
 echo htmlspecialchars($patient['contact_number']); ?>">
                        </div>
                        <div class="form-group full-width">
                            <label>Email Address</label>
                            <input type="email" class="form-control" name="email" value="<?php
 echo htmlspecialchars($patient['email']); ?>">
                        </div>
                        <div class="form-group full-width">
                            <label>Address</label>
                            <textarea class="form-control" name="address"><?php
 echo htmlspecialchars($patient['address']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Emergency Contact</label>
                            <input type="text" class="form-control" name="emergency_contact" value="<?php
 echo htmlspecialchars($patient['emergency_contact']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Occupation</label>
                            <input type="text" class="form-control" name="occupation" value="<?php
 echo htmlspecialchars($patient['occupation']); ?>">
                        </div>
                    </div>

                    <h3 class="form-section-title"><i class="fa-solid fa-notes-medical"></i> Medical & Dental Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Blood Type</label>
                            <select class="form-control" name="blood_type">
                                <?php

                                $btypes = ['Unknown', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                                foreach($btypes as $bt) {
                                    $sel = ($patient['blood_type'] == $bt) ? 'selected' : '';
                                    echo "<option value='$bt' $sel>$bt</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Smoking Status</label>
                            <select class="form-control" name="smoking_status">
                                <?php

                                $smoke = ['Non-Smoker', 'Occasional', 'Heavy Smoker'];
                                foreach($smoke as $s) {
                                    $sel = ($patient['smoking_status'] == $s) ? 'selected' : '';
                                    echo "<option value='$s' $sel>$s</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label>Allergies</label>
                            <input type="text" class="form-control" name="allergies" value="<?php
 echo htmlspecialchars($patient['allergies']); ?>">
                        </div>
                        <div class="form-group full-width">
                            <label>Medical Conditions</label>
                            <textarea class="form-control" name="medical_conditions"><?php
 echo htmlspecialchars($patient['medical_conditions']); ?></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label>Current Medications</label>
                            <textarea class="form-control" name="current_medications"><?php
 echo htmlspecialchars($patient['current_medications']); ?></textarea>
                        </div>
                        <div class="form-group full-width">
                            <label>Pregnancy (For Female Patients)</label>
                            <select class="form-control" name="pregnancy">
                                <?php

                                $preg = ['Not Applicable / No', 'Yes', 'Trying to conceive'];
                                foreach($preg as $p) {
                                    $sel = ($patient['pregnancy'] == $p) ? 'selected' : '';
                                    echo "<option value='$p' $sel>$p</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancelEditModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Patient Record</button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/patient_profile.js"></script>
    
    <!-- Tooth Details Modal -->
    <div class="modal-overlay" id="toothModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Tooth #<span id="modalToothNumberDisplay"></span> Details</h2>
                <button class="close-modal" id="closeToothModalBtn">&times;</button>
            </div>
            
            <div class="modal-body">
                <!-- History Section -->
                <h3 class="form-section-title" style="margin-top:0;"><i class="fa-solid fa-clock-rotate-left"></i> Treatment History</h3>
                <div class="history-list" id="toothHistoryList">
                    <!-- Dynamic history items loaded via AJAX -->
                    <div style="padding:12px; text-align:center; color:gray;">Loading history...</div>
                </div>

                <hr style="margin:24px 0; border:none; border-top:1px solid #e2e8f0;">

                <!-- Add New Record Form -->
                <h3 class="form-section-title"><i class="fa-solid fa-notes-medical"></i> Add New Record</h3>
                <form id="addToothRecordForm">
                    <input type="hidden" name="patient_id" value="<?php
 echo htmlspecialchars($patient['patient_id']); ?>">
                    <input type="hidden" name="tooth_number" id="modalToothNumberInput" value="">
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="modalToothStatus" required>
                            <option value="Healthy">Healthy</option>
                            <option value="Cavity">Cavity</option>
                            <option value="Filling">Filling</option>
                            <option value="Root Canal">Root Canal</option>
                            <option value="Crown">Crown</option>
                            <option value="Missing">Missing</option>
                            <option value="Extracted">Extracted</option>
                            <option value="Implant">Implant</option>
                            <option value="Bridge">Bridge</option>
                            <option value="Wisdom Tooth">Wisdom Tooth</option>
                            <option value="Fractured">Fractured</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Diagnosis</label>
                        <input type="text" class="form-control" name="diagnosis" placeholder="e.g. Deep caries on occlusal surface">
                    </div>
                    
                    <div class="form-group">
                        <label>Treatment Performed</label>
                        <input type="text" class="form-control" name="treatment" placeholder="e.g. Composite filling">
                    </div>
                    
                    <div class="form-group">
                        <label>Dentist Name</label>
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
                                $stmtDentists = $pdo->query("SELECT username FROM users WHERE role = 'Dentist' ORDER BY username ASC");
                                $dentists = $stmtDentists->fetchAll(PDO::FETCH_ASSOC);
                                foreach($dentists as $d) {
                                    echo "<option value='" . htmlspecialchars($d['username']) . "'>" . htmlspecialchars($d['username']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" placeholder="Additional observations..."></textarea>
                    </div>

                    <div style="margin-top:16px; text-align:right;">
                        <button type="submit" class="btn btn-primary">Save Tooth Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

