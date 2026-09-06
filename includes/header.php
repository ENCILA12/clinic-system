<header class="top-header">
    <div class="search-bar">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search patients, appointments...">
    </div>
    <div class="header-right">
        <div class="notification-btn" id="notifBtn" style="position: relative; cursor: pointer;">
            <i class="fa-regular fa-bell"></i>
            <span class="badge" id="notifBadge" style="display:none;">0</span>
            
            <div class="notification-dropdown" id="notifDropdown">
                <div class="notif-header">Notifications</div>
                <div class="notif-body" id="notifList">
                    <!-- Notifications will be loaded here -->
                </div>
            </div>
        </div>
        <div class="user-profile">
            <?php
            $headerUser = $_SESSION['username'] ?? 'User';
            $headerRole = $_SESSION['role'] ?? 'Role';
            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($headerUser) . "&background=0D8ABC&color=fff";
            ?>
            <img src="<?php echo $avatarUrl; ?>" alt="User">
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($headerUser); ?></span>
                <span class="user-role"><?php echo htmlspecialchars($headerRole); ?></span>
            </div>
        </div>
    </div>
</header>

<style>
.notification-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 300px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    display: none;
    z-index: 1000;
    margin-top: 10px;
}
.notification-dropdown.active {
    display: block;
}
.notif-header {
    padding: 12px 16px;
    font-weight: 600;
    border-bottom: 1px solid #e2e8f0;
    color: #1e293b;
}
.notif-item {
    display: flex;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none;
    color: #334155;
    transition: background 0.2s;
}
.notif-item:hover {
    background: #f8fafc;
}
.notif-item:last-child {
    border-bottom: none;
}
.notif-icon {
    width: 32px;
    height: 32px;
    background: #e0f2fe;
    color: #0ea5e9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.notif-title {
    font-weight: 500;
    font-size: 13px;
    color: #0f172a;
    margin-bottom: 2px;
}
.notif-msg {
    font-size: 12px;
    color: #64748b;
}
.notif-empty {
    padding: 24px;
    text-align: center;
    color: #94a3b8;
    font-size: 13px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifBadge = document.getElementById('notifBadge');
    const notifList = document.getElementById('notifList');

    // Toggle dropdown
    notifBtn.addEventListener('click', function(e) {
        if (!e.target.closest('.notification-dropdown')) {
            notifDropdown.classList.toggle('active');
        }
    });

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!notifBtn.contains(e.target)) {
            notifDropdown.classList.remove('active');
        }
    });

    // Fetch notifications
    fetch('api/get_notifications.php')
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.count > 0) {
                    notifBadge.innerText = data.count;
                    notifBadge.style.display = 'flex';
                    
                    let html = '';
                    data.notifications.forEach(n => {
                        html += `
                            <a href="${n.link}" class="notif-item">
                                <div class="notif-icon"><i class="fa-solid ${n.icon}"></i></div>
                                <div>
                                    <div class="notif-title">${n.title}</div>
                                    <div class="notif-msg">${n.message}</div>
                                </div>
                            </a>
                        `;
                    });
                    notifList.innerHTML = html;
                } else {
                    notifList.innerHTML = '<div class="notif-empty"><i class="fa-regular fa-bell-slash" style="font-size:24px; margin-bottom:8px;"></i><br>No new notifications</div>';
                }
            }
        });
});
</script>
