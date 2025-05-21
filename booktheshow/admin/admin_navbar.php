<nav>
    <div class="logo"><span>BookTheShow</span></div>
    <div class="nav-right">
        <span class="admin-welcome">Halo, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_manage.php">Kelola Data</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>


