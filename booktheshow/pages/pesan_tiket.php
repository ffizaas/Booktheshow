<?php
// File: pages/pesan_tiket.php

require_once __DIR__ . '/../includes/koneksi.php';
session_start();

// Jika tidak ada parameter jadwal_id, tampilkan pesan dan form kosong
$jadwal_id = isset($_GET['jadwal_id']) ? filter_input(INPUT_GET, 'jadwal_id', FILTER_VALIDATE_INT) : null;

$jadwal = null;
if ($jadwal_id) {
    try {
        // Ambil data jadwal spesifik
        $stmt = $conn->prepare("
            SELECT j.id, j.pertunjukan_id, p.judul, j.tanggal, j.waktu, j.sisa_tiket 
            FROM jadwal j 
            JOIN pertunjukan p ON j.pertunjukan_id = p.id
            WHERE j.id = ? AND j.sisa_tiket > 0
        ");
        $stmt->execute([$jadwal_id]);
        $jadwal = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$jadwal) {
            $_SESSION['error'] = "Jadwal tidak tersedia atau tiket sudah habis";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Gagal mengambil data jadwal";
        error_log("Database error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Tiket - BookTheShow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php
    if (isset($_SESSION['admin'])) {
        include '../admin/admin_navbar.php';
    } else {
        include '../includes/navbar.php';
    }
    ?>

    <div class="container">
        <h1>Pesan Tiket</h1>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="booking-container">
            <?php if ($jadwal): ?>
                <h2><?= htmlspecialchars($jadwal['judul']) ?></h2>
                <p>Tanggal: <?= date('d M Y', strtotime($jadwal['tanggal'])) ?></p>
                <p>Waktu: <?= date('H:i', strtotime($jadwal['waktu'])) ?></p>
                <p>Sisa Tiket: <?= $jadwal['sisa_tiket'] ?></p>
                
                <form action="../proses_pemesanan.php" method="POST">
                    <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                    <input type="hidden" name="pertunjukan_id" value="<?= $jadwal['pertunjukan_id'] ?>">
                    
                    <div class="form-group">
                        <label for="nama">Nama Pemesan:</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Pemesan:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="jumlah_tiket">Jumlah Tiket:</label>
                        <input type="number" id="jumlah_tiket" name="jumlah_tiket" min="1" max="<?= $jadwal['sisa_tiket'] ?>" required>
                    </div>

                    <button type="submit" class="btn btn-block">Pesan Tiket</button>
                </form>
            <?php else: ?>
                <div class="info-message">
                    <p>Silakan pilih pertunjukan dari <a href="jadwal.php">Jadwal</a> untuk memesan tiket.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>