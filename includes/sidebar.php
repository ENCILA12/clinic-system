<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? 'Unknown';
$username = $_SESSION['username'] ?? 'User';
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-tooth"></i> SmileCare Clinic
    </div>
    
    <div style="padding: 10px 20px; color: #94a3b8; font-size: 13px; border-bottom: 1px solid #334155; margin-bottom: 10px;">
        Logged in as: <br><strong style="color:white;"><?php echo htmlspecialchars($username); ?></strong> (<?php echo $role; ?>)
    </div>

    <ul class="sidebar-nav">
        <!-- Dashboard: All Roles -->
        <li class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>"><a href="index.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
        
        <!-- Appointments: Admin, Receptionist, Dentist, Assistant -->
        <li class="<?php echo ($currentPage == 'appointments.php') ? 'active' : ''; ?>"><a href="appointments.php"><i class="fa-solid fa-calendar-check"></i> Appointments</a></li>
        
        <!-- Patients: Admin, Receptionist -->
        <?php if(in_array($role, ['Admin', 'Receptionist'])): ?>
        <li class="<?php echo ($currentPage == 'patients.php') ? 'active' : ''; ?>"><a href="patients.php"><i class="fa-solid fa-users"></i> Patients</a></li>
        <?php endif; ?>

        <!-- Treatments: Admin, Dentist, Assistant -->
        <?php if(in_array($role, ['Admin', 'Dentist', 'Assistant'])): ?>
        <li class="<?php echo ($currentPage == 'treatments.php') ? 'active' : ''; ?>"><a href="treatments.php"><i class="fa-solid fa-notes-medical"></i> Treatments</a></li>
        <?php endif; ?>

        <!-- Billing: Admin, Receptionist -->
        <?php if(in_array($role, ['Admin', 'Receptionist'])): ?>
        <li class="<?php echo ($currentPage == 'billing.php') ? 'active' : ''; ?>"><a href="billing.php"><i class="fa-solid fa-file-invoice-dollar"></i> Billing</a></li>
        <?php endif; ?>

        <!-- Dentists: Admin -->
        <?php if(in_array($role, ['Admin'])): ?>
        <li class="<?php echo ($currentPage == 'dentists.php') ? 'active' : ''; ?>"><a href="dentists.php"><i class="fa-solid fa-user-doctor"></i> Dentists</a></li>
        <?php endif; ?>

        <!-- Inventory: Admin, Assistant -->
        <?php if(in_array($role, ['Admin', 'Assistant'])): ?>
        <li class="<?php echo ($currentPage == 'inventory.php') ? 'active' : ''; ?>"><a href="inventory.php"><i class="fa-solid fa-box-open"></i> Inventory</a></li>
        <?php endif; ?>

        <!-- Settings: Admin -->
        <?php if(in_array($role, ['Admin'])): ?>
        <li class="<?php echo ($currentPage == 'settings.php') ? 'active' : ''; ?>"><a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
        <?php endif; ?>

        <!-- Logout -->
        <li><a href="logout.php" style="color:#ef4444;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</aside>
