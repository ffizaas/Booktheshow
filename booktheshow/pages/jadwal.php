<?php 
// File: pages/jadwal.php

require_once __DIR__ . '/../includes/koneksi.php';
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pertunjukan - BookTheShow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <h1>Jadwal Pertunjukan</h1>
        
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        
        if (isset($_SESSION['success'])) {
            echo '<div class="alert success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        
        try {
            $stmt = $conn->prepare("
                SELECT j.id, p.judul, p.deskripsi, p.gambar, j.tanggal, j.waktu, j.sisa_tiket 
                FROM jadwal j 
                JOIN pertunjukan p ON j.pertunjukan_id = p.id
                WHERE j.sisa_tiket > 0
                ORDER BY j.tanggal, j.waktu
            ");
            $stmt->execute();
            $jadwals = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($jadwals)) {
                echo "<p class='info'>Tidak ada jadwal tersedia saat ini.</p>";
            } else {
                echo '<div class="jadwal-list">';
                foreach($jadwals as $jadwal) {
                    echo '<div class="jadwal-card">
                            <img src="../uploads/' . htmlspecialchars($jadwal['gambar']) . '" alt="' . htmlspecialchars($jadwal['judul']) . '">
                            <div class="jadwal-content">
                                <h3>' . htmlspecialchars($jadwal['judul']) . '</h3>
                                <p class="jadwal-description">' . htmlspecialchars(substr($jadwal['deskripsi'], 0, 150)) . '...</p>
                                <div class="jadwal-detail">
                                    <p><strong>Tanggal:</strong> ' . date('d M Y', strtotime($jadwal['tanggal'])) . '</p>
                                    <p><strong>Waktu:</strong> ' . date('H:i', strtotime($jadwal['waktu'])) . '</p>
                                    <p><strong>Sisa Tiket:</strong> ' . $jadwal['sisa_tiket'] . '</p>
                                </div>
                                <a href="pesan_tiket.php?jadwal_id=' . $jadwal['id'] . '" class="btn">Pesan Tiket</a>
                            </div>
                          </div>';
                }
                echo '</div>';
            }
        } catch (PDOException $e) {
            echo '<div class="alert error">Gagal mengambil data jadwal. Silakan coba lagi.</div>';
            error_log("Database error: " . $e->getMessage());
        }
        ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>