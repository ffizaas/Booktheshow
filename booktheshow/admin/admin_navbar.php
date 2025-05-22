<nav>
    <div class="logo"><span>BookTheShow</span></div>
    <div class="nav-admin">
    <div class="admin-profile">
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_manage.php">Kelola Data</a>
        <span class="admin-avatar"><?= strtoupper(substr(($_SESSION['username'] ?? 'A'), 0, 1)) ?></span>
        <a href="/booktheshow/admin/logout.php" class="logout-btn">Logout</a>
    </class=>
</nav>
