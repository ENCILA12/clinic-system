<?php
$files = ['appointments.php', 'billing.php', 'inventory.php', 'dentists.php'];
foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Replace SELECT * FROM table ORDER BY... with WHERE clinic_id = ?
    // This is hard to do with regex reliably.
    echo "Check $file manually.\n";
}
?>
