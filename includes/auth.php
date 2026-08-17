<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userRole = $_SESSION['role'] ?? 'Unknown';
$currentPage = basename($_SERVER['PHP_SELF']);

// Define Access Rules (which roles can see which pages)
$accessRules = [
    'Admin' => ['*'], // * means all pages
    'Receptionist' => ['index.php', 'patients.php', 'appointments.php', 'billing.php', 'patient_profile.php'],
    'Dentist' => ['index.php', 'patient_profile.php', 'treatments.php', 'appointments.php'],
    'Assistant' => ['index.php', 'appointments.php', 'inventory.php', 'treatments.php']
];

function hasAccess($role, $page, $rules) {
    if (!isset($rules[$role])) return false;
    if (in_array('*', $rules[$role])) return true;
    return in_array($page, $rules[$role]);
}

// Ignore auth check for logout.php and login.php just in case
$ignoredPages = ['login.php', 'logout.php'];

if (!in_array($currentPage, $ignoredPages)) {
    if (!hasAccess($userRole, $currentPage, $accessRules)) {
        // Redirect to dashboard or show unauthorized message
        // Just redirect to index.php if not allowed
        if ($currentPage !== 'index.php') {
            header("Location: index.php");
            exit;
        }
    }
}
?>
