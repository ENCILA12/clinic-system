<?php
// router.php - Handles URL Rewrites for PHP Built-in Server
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Check if the URL matches the /clinic-slug/login.php pattern
if (preg_match('/^\/CLINIC%20system\/([a-zA-Z0-9-]+)\/login\.php$/', $path, $matches) || preg_match('/^\/([a-zA-Z0-9-]+)\/login\.php$/', $path, $matches)) {
    $_GET['clinic_slug'] = $matches[1];
    $_SERVER['SCRIPT_NAME'] = '/login.php';
    require 'login.php';
    return true; // Script handled the request
}

// For all other requests, let the built-in server handle them
return false;
?>
