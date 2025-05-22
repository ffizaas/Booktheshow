<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/koneksi.php';

try {
    $pertunjukan_count = $conn->query("SELECT COUNT(*) FROM pertunjukan")->fetchColumn();
    $jadwal_count = $conn->query("SELECT COUNT(*) FROM jadwal")->fetchColumn();
    $pemesanan_count = $conn->query("SELECT SUM(jumlah_tiket) FROM pemesanan")->fetchColumn();
    if ($pemesanan_count === null) {
        $pemesanan_count = 0;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BookTheShow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>

    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Pertunjukan</h3>
                <p><?= $pertunjukan_count ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Jadwal</h3>
                <p><?= $jadwal_count ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Pemesanan</h3>
                <a href="riwayat_pemesanan.php">
                    <p> <?= $pemesanan_count ?></p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>

