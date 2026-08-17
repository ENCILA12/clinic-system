<?php
require_once 'includes/db.php';

// Fetch dentists for the dropdown
$stmtDentists = $pdo->query("SELECT username FROM users WHERE role = 'Dentist' ORDER BY username ASC");
$dentists = $stmtDentists->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Book an Appointment - Dental Clinic</title>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <style>
        :root {
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --bg: #f0f9ff;
            --surface: #ffffff;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --radius: 12px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .booking-container { background: var(--surface); max-width: 500px; width: 100%; border-radius: var(--radius); padding: 40px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: var(--primary); font-size: 24px; margin-bottom: 8px; }
        .header p { color: var(--text-muted); font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 6px; color: #334155; }
        .form-control { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; transition: 0.2s; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1); }
        .btn { width: 100%; padding: 14px; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.2s; margin-top: 10px; }
        .btn:hover { background: var(--primary-hover); }
        .btn:disabled { background: #94a3b8; cursor: not-allowed; }
        .success-message { display: none; text-align: center; padding: 30px 20px; }
        .success-message i { font-size: 50px; color: #22c55e; margin-bottom: 16px; }
        .success-message h2 { margin-bottom: 8px; }
        .success-message p { color: var(--text-muted); }
    </style>
</head>
<body>

    <div class="booking-container" id="bookingBox">
        <div class="header">
            <i class="fa-solid fa-tooth" style="font-size: 40px; color: var(--primary); margin-bottom: 16px;"></i>
            <h1>Book Appointment</h1>
            <p>Fill out the form below to request a schedule. We will review and confirm it shortly.</p>
        </div>

        <form id="publicBookingForm">
            <!-- Patient Info -->
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" class="form-control" name="full_name" placeholder="Juan Dela Cruz" required>
            </div>
            <div class="form-group" style="display:flex; gap:16px;">
                <div style="flex:1;">
                    <label>Contact Number</label>
                    <input type="text" class="form-control" name="contact_number" placeholder="09xxxxxxxxx" required>
                </div>
                <div style="flex:1;">
                    <label>Age</label>
                    <input type="number" class="form-control" name="age" placeholder="Age" min="1" required>
                </div>
            </div>

            <hr style="border:0; border-top:1px solid #e2e8f0; margin:24px 0;">

            <!-- Appointment Details -->
            <div class="form-group" style="display:flex; gap:16px;">
                <div style="flex:1;">
                    <label>Preferred Date</label>
                    <input type="date" class="form-control" name="appointment_date" id="aptDate" required>
                </div>
                <div style="flex:1;">
                    <label>Preferred Time</label>
                    <input type="time" class="form-control" name="appointment_time" required>
                </div>
            </div>

            <div class="form-group">
                <label>Procedure Needed</label>
                <input list="procedureList" class="form-control" name="procedure_name" placeholder="Search or select..." required>
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
                <label>Preferred Dentist (Optional)</label>
                <select class="form-control" name="dentist_name">
                    <option value="">Any Available Dentist</option>
                    <?php foreach($dentists as $d): ?>
                        <option value="<?php echo htmlspecialchars($d['username']); ?>"><?php echo htmlspecialchars($d['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Additional Notes / Concerns</label>
                <textarea class="form-control" name="notes" placeholder="Tell us what you feel or need..."></textarea>
            </div>

            <button type="submit" class="btn" id="submitBtn">Submit Request</button>
        </form>
        
        <!-- Success State -->
        <div class="success-message" id="successState">
            <i class="fa-solid fa-circle-check"></i>
            <h2>Request Sent!</h2>
            <p>Your appointment request has been submitted to our clinic. Please wait for our confirmation via text or call.</p>
            <br>
            <button class="btn" style="background:#e2e8f0; color:#334155;" onclick="location.reload()">Book Another</button>
        </div>
    </div>

    <script src="js/book.js"></script>
</body>
</html>
