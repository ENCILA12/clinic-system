<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userRole = $_SESSION['role'] ?? 'Unknown';
$currentPage = basename($_SERVER['PHP_SELF']);

// Enforce slug routing
if (isset($_SESSION['clinic_slug']) && $_SESSION['clinic_slug'] !== '' && $_SESSION['role'] !== 'Superadmin') {
    $base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if ($base_url === '/') $base_url = '';
    
    // The expected path prefix
    $expected_prefix = $base_url . '/' . $_SESSION['clinic_slug'] . '/';
    
    // If the URL doesn't contain the expected prefix and we are not logging out or accessing superadmin
    $isApiRequest = (strpos($_SERVER['REQUEST_URI'], '/api/') !== false || strpos($_SERVER['SCRIPT_NAME'], '/api/') !== false);
    
    if (!$isApiRequest && strpos($_SERVER['REQUEST_URI'], $expected_prefix) !== 0 && $currentPage !== 'logout.php' && $currentPage !== 'login.php' && $currentPage !== 'superadmin.php') {
        // Force redirect to their correct URL
        header("Location: " . $expected_prefix . $currentPage);
        exit;
    }
}

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
