<?php
$files = [
    'api/save_appointment.php' => ['INSERT INTO appointments (', 'INSERT INTO appointments (clinic_id, ', ', :notes', ', :notes, :clinic_id', "':notes' => \$_POST['notes'] ?? ''", "':notes' => \$_POST['notes'] ?? '',\n            ':clinic_id' => \$_SESSION['clinic_id']"],
    'api/save_dentist.php' => ['INSERT INTO dentists (', 'INSERT INTO dentists (clinic_id, ', 'is_available)', 'is_available, clinic_id)', '?)', '?, ?)'],
    'api/save_inventory.php' => ['INSERT INTO inventory (', 'INSERT INTO inventory (clinic_id, ', 'expiration_date)', 'expiration_date, clinic_id)', '?)', '?, ?)'],
    'api/save_patient.php' => ['INSERT INTO patients (', 'INSERT INTO patients (clinic_id, ', 'pregnancy', 'pregnancy, clinic_id', ':pregnancy', ':pregnancy, :clinic_id', "':pregnancy' => \$_POST['pregnancy'] ?? ''", "':pregnancy' => \$_POST['pregnancy'] ?? '',\n            ':clinic_id' => \$_SESSION['clinic_id']"],
    'api/save_tooth_record.php' => ['INSERT INTO dental_records (', 'INSERT INTO dental_records (clinic_id, ', 'notes', 'notes, clinic_id', ':notes', ':notes, :clinic_id', "':notes' => \$notes", "':notes' => \$notes,\n            ':clinic_id' => \$_SESSION['clinic_id']"],
    'api/save_treatment.php' => ['INSERT INTO treatments (', 'INSERT INTO treatments (clinic_id, ', 'materials_used', 'materials_used, clinic_id', ':materials_used', ':materials_used, :clinic_id', "':materials_used' => \$materialsUsed", "':materials_used' => \$materialsUsed,\n            ':clinic_id' => \$_SESSION['clinic_id']"],
    'api/save_user.php' => ['INSERT INTO users (', 'INSERT INTO users (clinic_id, ', 'role)', 'role, clinic_id)', '?)', '?, ?)']
];

foreach ($files as $f => $replacements) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        
        // Ensure session_start() exists
        if (!preg_match('/session_start\(\)/', $c)) {
            $c = preg_replace('/<\?php/', "<?php\nsession_start();", $c);
        }
        
        for ($i=0; $i < count($replacements); $i+=2) {
            $c = str_replace($replacements[$i], $replacements[$i+1], $c);
        }
        
        // For positional params (prepare with ?) in save_dentist, save_inventory, save_user
        if ($f == 'api/save_dentist.php') {
            $c = str_replace('[$name, $specialization, $prc_license, $scheduleJson, $consultation_fee, $is_available]', '[$name, $specialization, $prc_license, $scheduleJson, $consultation_fee, $is_available, $_SESSION[\'clinic_id\']]', $c);
        }
        if ($f == 'api/save_inventory.php') {
            $c = str_replace('[$name, $stock, $min, $exp]', '[$name, $stock, $min, $exp, $_SESSION[\'clinic_id\']]', $c);
        }
        if ($f == 'api/save_user.php') {
            $c = str_replace('[$username, $full_name, $hashedPassword, $role]', '[$username, $full_name, $hashedPassword, $role, $_SESSION[\'clinic_id\']]', $c);
        }
        
        file_put_contents($f, $c);
        echo "Fixed $f\n";
    }
}
?>
