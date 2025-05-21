<?php 
// File: pages/index.php

require_once __DIR__ . '/../includes/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookTheShow - Beranda</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="hero">
        <img src="../assets/images/hero-teater.png" alt="Teater" class="hero-image">
        <div class="hero-text">
            <h1>Selamat Datang di BookTheShow!</h1>
            <p>Temukan pertunjukan teater favoritmu.</p>
        </div>
    </section>

    <div class="container">
        <h2>Daftar Pertunjukan</h2>
        <div class="pertunjukan-list">
            <?php
            try {
                $stmt = $conn->query("SELECT * FROM pertunjukan LIMIT 6");
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="pertunjukan">
                            <img src="../uploads/' . htmlspecialchars($row['gambar']) . '" alt="' . htmlspecialchars($row['judul']) . '">
                            <div class="pertunjukan-content">
                                <h3>' . htmlspecialchars($row['judul']) . '</h3>
                                <p>' . htmlspecialchars(substr($row['deskripsi'], 0, 100)) . '...</p>
                                <p class="duration">Durasi: ' . htmlspecialchars($row['durasi']) . ' menit</p>
                                <a href="jadwal.php?pertunjukan=' . $row['id'] . '" class="btn">Lihat Jadwal</a>
                            </div>
                          </div>';
                }
            } catch(PDOException $e) {
                echo '<p class="error">Gagal memuat daftar pertunjukan. Silakan coba lagi.</p>';
                error_log("Database error: " . $e->getMessage());
            }
            ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>