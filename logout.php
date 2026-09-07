<?php
session_start();

$slug = $_SESSION['clinic_slug'] ?? '';
$role = $_SESSION['role'] ?? '';

session_unset();
session_destroy();

if (!empty($slug)) {
    $base_url = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if ($base_url === '/') $base_url = '';
    header("Location: " . $base_url . "/" . $slug . "/login.php");
} else {
    header("Location: login.php");
}
exit;
?>
