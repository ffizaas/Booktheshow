<?php 
// File: pages/jadwal.php

require_once __DIR__ . '/../includes/koneksi.php';
session_start();

// Set header untuk memastikan tidak ada caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pertunjukan - BookTheShow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        <h1>Jadwal Pertunjukan</h1>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="filter-container">
            <form method="get" action="">
                <div class="search-wrapper">
                    <span class="search-icon">&#128269;</span>
                    <input type="search" id="search" name="search" class="search-bar" placeholder="Cari pertunjukan..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
            </form>
        </div>
        
        <?php
        try {
            // Build query dengan filter
            $query = "
                SELECT j.id, p.judul, p.deskripsi, p.gambar, j.tanggal, j.waktu, j.sisa_tiket 
                FROM jadwal j 
                JOIN pertunjukan p ON j.pertunjukan_id = p.id
                WHERE j.tanggal >= CURDATE()
            ";
            
            $params = [];
            
            // Tambahkan filter pencarian
            if (!empty($_GET['search'])) {
                $query .= " AND p.judul LIKE ?";
                $params[] = '%' . $_GET['search'] . '%';
            }
            
            $query .= " ORDER BY j.tanggal, j.waktu";
            
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $jadwals = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($jadwals)) {
                echo '<div class="no-result">
                        <i class="fas fa-calendar-times"></i>
                        <p>Tidak ada jadwal pertunjukan yang tersedia saat ini.</p>
                      </div>';
            } else {
                echo '<div class="pertunjukan-list">';
                foreach($jadwals as $jadwal) {
                    $isSoldOut = $jadwal['sisa_tiket'] <= 0;
                    $isPast = strtotime($jadwal['tanggal']) < strtotime(date('Y-m-d'));
                    
                    echo '<div class="pertunjukan' . 
                         ($isSoldOut ? ' sold-out' : '') . 
                         ($isPast ? ' past-event' : '') . 
                         '">
                            <div class="card-image">
                                <img src="../uploads/' . htmlspecialchars($jadwal['gambar']) . '" alt="' . htmlspecialchars($jadwal['judul']) . '" loading="lazy">
                                ' . ($isSoldOut ? '<div class="sold-out-badge">Habis</div>' : '') . '
                            </div>
                            <div class="pertunjukan-content">
                                <h3>' . htmlspecialchars($jadwal['judul']) . '</h3>
                                <div class="deskripsi-container">
                                    <p class="deskripsi-pendek">' . htmlspecialchars(substr($jadwal['deskripsi'], 0, 100)) . '...</p>
                                    <p class="deskripsi-lengkap">' . nl2br(htmlspecialchars($jadwal['deskripsi'])) . '</p>
                                    <a href="#" class="lihat-selengkapnya">Selengkapnya</a>
                                </div>
                                <div class="meta-info">
                                    <p><i class="fas fa-calendar-alt"></i> ' . date('d M Y', strtotime($jadwal['tanggal'])) . '</p>
                                    <p><i class="fas fa-clock"></i> ' . date('H:i', strtotime($jadwal['waktu'])) . '</p>
                                    <p><i class="fas fa-ticket-alt"></i> Sisa: ' . $jadwal['sisa_tiket'] . '</p>
                                </div>
                                <a href="pesan_tiket.php?jadwal_id=' . $jadwal['id'] . '" class="btn' . ($isSoldOut ? ' disabled' : '') . '">
                                    ' . ($isSoldOut ? 'Tiket Habis' : 'Pesan Sekarang') . '
                                </a>
                            </div>
                          </div>';
                }
                echo '</div>'; // Tutup pertunjukan-list
            }
        } catch (PDOException $e) {
            echo '<div class="alert error">Gagal memuat data jadwal. Silakan coba lagi nanti.</div>';
            error_log("Database error in jadwal.php: " . $e->getMessage());
        }
        ?>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script>
    // Disable link untuk tiket habis
    document.querySelectorAll('.btn.disabled').forEach(link => {
        link.addEventListener('click', e => e.preventDefault());
    });
    
    // Fungsi untuk deskripsi selengkapnya
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