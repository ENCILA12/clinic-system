<header class="top-header">
    <div class="search-bar">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search patients, appointments...">
    </div>
    <div class="header-right">
        <div class="notification-btn">
            <i class="fa-regular fa-bell"></i>
            <span class="badge">3</span>
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
