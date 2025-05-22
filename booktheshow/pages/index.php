<?php 
// File: pages/index.php

require_once __DIR__ . '/../includes/koneksi.php';
session_start();
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
    <?php
    // Perbaikan logika navbar
    if (isset($_SESSION['admin'])) {
        include '../admin/admin_navbar.php'; // Navbar admin
    } else {
        include '../includes/navbar.php'; // Navbar pengguna biasa
    }
    ?>

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
                    // Format durasi
                    $durasiMenit = (int)$row['durasi'];
                    $jam = floor($durasiMenit / 60);
                    $menit = $durasiMenit % 60;

                    $formatDurasi = '';
                    if ($jam > 0) {
                        $formatDurasi .= $jam . ' jam';
                    }
                    if ($menit > 0) {
                        if ($jam > 0) $formatDurasi .= ' ';
                        $formatDurasi .= $menit . ' menit';
                    }
                    if ($formatDurasi === '') {
                        $formatDurasi = '0 menit';
                    }

                    echo '<div class="pertunjukan">
                            <img src="../uploads/' . htmlspecialchars($row['gambar']) . '" alt="' . htmlspecialchars($row['judul']) . '">
                            <div class="pertunjukan-content">
                                <h3>' . htmlspecialchars($row['judul']) . '</h3>
                                <div class="deskripsi-container">
                                    <p class="deskripsi-pendek">' . htmlspecialchars(substr($row['deskripsi'], 0, 100)) . '...</p>
                                    <p class="deskripsi-lengkap">' . nl2br(htmlspecialchars($row['deskripsi'])) . '</p>
                                    <a href="#" class="lihat-selengkapnya">Selengkapnya</a>
                                </div>
                                <p class="duration">Durasi: ' . htmlspecialchars($formatDurasi) . '</p>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.lihat-selengkapnya').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const container = this.closest('.deskripsi-container');
                    const pendek = container.querySelector('.deskripsi-pendek');
                    const lengkap = container.querySelector('.deskripsi-lengkap');
                    
                    // Toggle class 'visible'
                    lengkap.classList.toggle('visible');
                    
                    // Ganti teks tombol
                    this.textContent = lengkap.classList.contains('visible') 
                        ? 'Lebih Sedikit' 
                        : 'Selengkapnya';
                    
                    // Sembunyikan deskripsi pendek jika lengkap ditampilkan
                    pendek.style.display = lengkap.classList.contains('visible') 
                        ? 'none' 
                        : 'block';
                });
            });
        });
    </script>
</body>
</html>