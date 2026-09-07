<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    die("Unauthorized or Missing ID.");
}
require_once 'includes/db.php';

$px_id = $_GET['id'];
$clinic_id = $_SESSION['clinic_id'];

// Get Clinic Details
$stmt = $pdo->prepare("SELECT name, logo_url FROM clinics WHERE id = ?");
$stmt->execute([$clinic_id]);
$clinic = $stmt->fetch(PDO::FETCH_ASSOC);

// Get Prescription & Patient Details
$stmtPx = $pdo->prepare("
    SELECT p.*, pt.full_name, pt.age, pt.address, pt.gender 
    FROM prescriptions p 
    JOIN patients pt ON p.patient_id = pt.patient_id 
    WHERE p.id = ? AND p.clinic_id = ?
");
$stmtPx->execute([$px_id, $clinic_id]);
$px = $stmtPx->fetch(PDO::FETCH_ASSOC);

if (!$px) die("Prescription not found.");

// Get Medicines
$stmtMeds = $pdo->prepare("SELECT * FROM prescription_items WHERE prescription_id = ?");
$stmtMeds->execute([$px_id]);
$meds = $stmtMeds->fetchAll(PDO::FETCH_ASSOC);

// Get Dentist License Info (Assuming license_number exists or mock it)
$stmtDent = $pdo->prepare("SELECT email FROM users WHERE username = ? AND role = 'Dentist' AND clinic_id = ?");
$stmtDent->execute([$px['dentist_name'], $clinic_id]);
$dentist = $stmtDent->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription - <?php echo htmlspecialchars($px['full_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; color: #1e293b; line-height: 1.5; background: #f8fafc; margin: 0; padding: 20px; }
        .prescription-paper { max-width: 600px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-top: 8px solid #0284c7; }
        .header { display: flex; align-items: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 16px; }
        .clinic-name { font-size: 24px; font-weight: 700; margin: 0; color: #0f172a; }
        .doctor-info { text-align: right; margin-left: auto; }
        .doctor-name { font-size: 18px; font-weight: 600; margin: 0; }
        
        .patient-info { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 30px; font-size: 14px; }
        .rx-symbol { font-size: 48px; font-weight: 700; color: #0284c7; font-family: serif; margin-bottom: 20px; line-height: 1; }
        
        .medicine-list { margin-bottom: 40px; }
        .medicine-item { margin-bottom: 16px; }
        .med-name { font-weight: 600; font-size: 16px; }
        .med-sig { font-size: 14px; color: #475569; margin-left: 20px; }
        
        .footer { border-top: 1px solid #e2e8f0; padding-top: 20px; display: flex; justify-content: space-between; align-items: flex-end; }
        .signature-line { border-bottom: 1px solid #000; width: 200px; margin-bottom: 4px; }
        .license { font-size: 12px; color: #64748b; }
        
        @media print {
            body { background: white; padding: 0; }
            .prescription-paper { box-shadow: none; max-width: 100%; border-top: 8px solid #000; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div style="text-align:center; margin-bottom:20px;" class="btn-print">
        <button onclick="window.print()" style="padding:10px 20px; background:#0284c7; color:white; border:none; border-radius:4px; cursor:pointer; font-size:16px;">
            <svg style="width:16px; height:16px; display:inline; vertical-align:middle; margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Prescription
        </button>
    </div>

    <div class="prescription-paper">
        <!-- Header -->
        <div class="header">
            <?php if (!empty($clinic['logo_url'])): ?>
                <img src="<?php echo htmlspecialchars($clinic['logo_url']); ?>" class="logo">
            <?php endif; ?>
            <div>
                <h1 class="clinic-name"><?php echo htmlspecialchars($clinic['name']); ?></h1>
                <div style="font-size:12px; color:#64748b;">Dental Clinic & Services</div>
            </div>
            
            <div class="doctor-info">
                <h2 class="doctor-name"><?php echo htmlspecialchars($px['dentist_name']); ?></h2>
                <div style="font-size:12px; color:#64748b;">Dental Surgeon</div>
            </div>
        </div>

        <!-- Patient Details -->
        <div class="patient-info">
            <div><strong>Patient Name:</strong> <?php echo htmlspecialchars($px['full_name']); ?></div>
            <div style="text-align:right;"><strong>Date:</strong> <?php echo date('M d, Y', strtotime($px['created_at'])); ?></div>
            <div><strong>Age/Sex:</strong> <?php echo htmlspecialchars($px['age']); ?> / <?php echo htmlspecialchars($px['gender']); ?></div>
            <div style="text-align:right;"><strong>Address:</strong> <?php echo htmlspecialchars($px['address']) ?: 'N/A'; ?></div>
        </div>

        <!-- Rx Body -->
        <div class="rx-symbol">Rx</div>
        
        <div class="medicine-list">
            <?php foreach($meds as $m): ?>
            <div class="medicine-item">
                <div class="med-name">
                    <?php echo htmlspecialchars($m['medicine_name']); ?> 
                    <?php if($m['dosage']) echo "- " . htmlspecialchars($m['dosage']); ?>
                </div>
                <div class="med-sig">
                    Sig: Take <?php echo htmlspecialchars($m['frequency']); ?> for <?php echo htmlspecialchars($m['duration']); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if(!empty($px['notes'])): ?>
        <div style="margin-bottom:30px; font-size:14px;">
            <strong>Notes:</strong><br>
            <?php echo nl2br(htmlspecialchars($px['notes'])); ?>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <div class="license">
                Not valid without dry seal.<br>
                For dental use only.
            </div>
            <div style="text-align:center;">
                <div class="signature-line"></div>
                <strong><?php echo htmlspecialchars($px['dentist_name']); ?></strong><br>
                <span class="license">PRC License No. ________</span>
            </div>
        </div>
    </div>
</body>
</html>
