<?php 
// File: pages/tentang.php

require_once __DIR__ . '/../includes/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang - BookTheShow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <h1>Tentang BookTheShow</h1>
        
        <div class="about-content">
            <p>BookTheShow adalah sistem pemesanan tiket teater berbasis web yang memudahkan pengguna untuk memesan tiket pertunjukan teater secara online.</p>
            
            <h2>Fitur-fitur Utama:</h2>
            <ul>
                <li>Pemesanan tiket secara langsung untuk pertunjukan teater</li>
                <li>Melihat jadwal pertunjukan yang tersedia</li>
                <li>Antarmuka yang mudah digunakan</li>
                <li>Sistem manajemen untuk administrator</li>
            </ul>
            
            <h2>Tim Pengembang:</h2>
            <p>Sistem ini dikembangkan sebagai proyek kuliah untuk mempelajari pengembangan web dengan PHP dan MySQL.</p>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>