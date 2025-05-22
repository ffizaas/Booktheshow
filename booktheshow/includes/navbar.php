<?php
// File: includes/navbar.php

$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav>
    <div class="logo"><a href="../pages/index.php">BookTheShow</a></div>
    
    <div class="nav-links">
        <a href="../pages/index.php" <?= ($current_page == 'index.php') ? 'class="active"' : '' ?>>Home</a>
        <a href="../pages/jadwal.php" <?= ($current_page == 'jadwal.php') ? 'class="active"' : '' ?>>Jadwal</a>
        <a href="../pages/pesan_tiket.php" <?= ($current_page == 'pesan_tiket.php') ? 'class="active"' : '' ?>>Pesan Tiket</a>
        <a href="../pages/tentang.php" <?= ($current_page == 'tentang.php') ? 'class="active"' : '' ?>>Tentang</a>
    </div>
    
    <div class="nav-admin">
        <?php if(isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
            <a href="../admin/admin_dashboard.php">Admin Dashboard</a>
            <a href="../admin/logout.php" class="logout-btn">Logout</a>
            
        <?php elseif (isset($_SESSION['user'])): ?>
            <a href="../admin/logout.php" class="logout-btn">Logout</a>
        <?php endif; ?>
    </div>
</nav>