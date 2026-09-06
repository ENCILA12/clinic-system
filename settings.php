<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Check if user is Admin
if ($_SESSION['role'] !== 'Admin') {
    echo "Access Denied.";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, username, full_name, role, created_at FROM users WHERE clinic_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['clinic_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch clinic info
    $stmtC = $pdo->prepare("SELECT name, logo_url FROM clinics WHERE id = ?");
    $stmtC->execute([$_SESSION['clinic_id']]);
    $clinic = $stmtC->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dental Clinic - Settings</title>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <link rel='stylesheet' href='css/style.css'>
</head>
<body>
    <div class='dashboard-container'>
        <?php include 'includes/sidebar.php'; ?>
        <div class='main-content'>
            <?php include 'includes/header.php'; ?>
            <main class='content-area'>
                <div class='page-header'>
                    <h1>Settings & Security</h1>
                    <p>Manage system access and user accounts.</p>
                </div>
                
                <div style="display:flex; gap:24px;">
                    <!-- Clinic Profile Section -->
                    <div style="flex: 1; background:white; border-radius:12px; border:1px solid var(--border-color); padding:24px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); align-self:flex-start;">
                        <h3 style="margin-top:0; border-bottom:1px solid var(--border-color); padding-bottom:12px;"><i class="fa-solid fa-hospital"></i> Clinic Profile</h3>
                        <form id="clinicProfileForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Clinic Name</label>
                                <input type="text" class="form-control" name="clinic_name" value="<?php echo htmlspecialchars($clinic['name'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Clinic Logo (Image only)</label>
                                <?php if (!empty($clinic['logo_url'])): ?>
                                    <div style="margin-bottom: 10px;">
                                        <img src="<?php echo htmlspecialchars($clinic['logo_url']); ?>" alt="Clinic Logo" style="max-height: 80px; max-width: 100%; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" name="clinic_logo" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary" style="width:100%;">Save Profile</button>
                        </form>
                    </div>

                    <!-- User List -->
                    <div class="data-table-container" style="flex: 2;">
                        <div style="padding:16px; border-bottom:1px solid var(--border-color); font-weight:600; font-size:16px;">
                            <i class="fa-solid fa-users"></i> System Accounts
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th style="text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users as $u): ?>
                                <tr>
                                    <td style="font-weight:600;"><?php echo htmlspecialchars($u['username']); ?></td>
                                    <td><?php echo htmlspecialchars($u['full_name'] ?? '-'); ?></td>
                                    <td>
                                        <span class="badge-tag" style="background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;">
                                            <?php echo htmlspecialchars($u['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                                    <td style="text-align:center;">
                                        <?php if($u['username'] !== 'admin'): ?>
                                        <button class="btn btn-outline" style="color:var(--danger); border-color:var(--danger);" onclick="deleteUser(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['username']); ?>')">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </button>
                                        <?php else: ?>
                                        <span style="font-size:12px; color:gray;">(Cannot Delete)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Add User Form -->
                    <div style="flex: 1; background:white; border-radius:12px; border:1px solid var(--border-color); padding:24px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); align-self:flex-start;">
                        <h3 style="margin-top:0; border-bottom:1px solid var(--border-color); padding-bottom:12px;"><i class="fa-solid fa-user-plus"></i> Add New Account</h3>
                        <form id="addUserForm">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" class="form-control" name="full_name" placeholder="e.g. Dr. John Smith" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select class="form-control" name="role" required>
                                    <option value="Admin">Admin</option>
                                    <option value="Receptionist">Receptionist</option>
                                    <option value="Dentist">Dentist</option>
                                    <option value="Assistant">Assistant</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width:100%;">Create Account</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        btn.innerText = 'Creating...';
        btn.disabled = true;

        const formData = new FormData(this);
        fetch('api/save_user.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => alert('An error occurred.'))
        .finally(() => {
            btn.innerText = 'Create Account';
            btn.disabled = false;
        });
    });

    document.getElementById('clinicProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        btn.innerText = 'Saving...';
        btn.disabled = true;

        const formData = new FormData(this);
        fetch('api/save_clinic_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => alert('An error occurred.'))
        .finally(() => {
            btn.innerText = 'Save Profile';
            btn.disabled = false;
        });
    });

    function deleteUser(id, username) {
        if(confirm("Are you sure you want to delete the account '" + username + "'? This cannot be undone.")) {
            const formData = new FormData();
            formData.append('id', id);
            
            fetch('api/delete_user.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('Account deleted.');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => alert('An error occurred.'));
        }
    }
    </script>
</body>
</html>
