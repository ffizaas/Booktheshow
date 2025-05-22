<?php
// File: proses_pemesanan.php

require_once __DIR__ . '/includes/koneksi.php';
session_start();

// Definisikan BASE_URL
define('BASE_URL', 'http://localhost/booktheshow/');

// Validasi metode request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Metode request tidak valid";
    header("Location: " . BASE_URL . "pages/pesan_tiket.php?jadwal_id=".$_POST['jadwal_id']);
    exit();
}

// Validasi input
$nama = trim(filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING));
$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$jadwal_id = filter_input(INPUT_POST, 'jadwal_id', FILTER_VALIDATE_INT);
$pertunjukan_id = filter_input(INPUT_POST, 'pertunjukan_id', FILTER_VALIDATE_INT);
$jumlah_tiket = filter_input(INPUT_POST, 'jumlah_tiket', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);

// Validasi data
if (empty($nama) || strlen($nama) < 3 || strlen($nama) > 100) {
    $_SESSION['error'] = "Nama harus antara 3-100 karakter";
    header("Location: " . BASE_URL . "pages/pesan_tiket.php?jadwal_id=".$jadwal_id);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Email tidak valid";
    header("Location: " . BASE_URL . "pages/pesan_tiket.php?jadwal_id=".$jadwal_id);
    exit();
}

if (!$jadwal_id || !$jumlah_tiket || !$pertunjukan_id) {
    $_SESSION['error'] = "Data jadwal, pertunjukan, atau jumlah tiket tidak valid";
    header("Location: " . BASE_URL . "pages/pesan_tiket.php?jadwal_id=".$jadwal_id);
    exit();
}

try {
    $conn->beginTransaction();
    
    // 1. Cek ketersediaan tiket
    $stmt = $conn->prepare("SELECT sisa_tiket FROM jadwal WHERE id = ? FOR UPDATE");
    $stmt->execute([$jadwal_id]);
    $sisa_tiket = $stmt->fetchColumn();
    
    if ($sisa_tiket === false) {
        throw new Exception("Jadwal tidak ditemukan");
    }
    
    if ($sisa_tiket < $jumlah_tiket) {
        throw new Exception("Maaf, hanya tersisa $sisa_tiket tiket");
    }
    
    // 2. Simpan pemesanan
    $stmt = $conn->prepare("INSERT INTO pemesanan (nama, email, jadwal_id, pertunjukan_id, jumlah_tiket, tanggal_pesan) 
                           VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$nama, $email, $jadwal_id, $pertunjukan_id, $jumlah_tiket]);
    
    // 3. Update stok
    $stmt = $conn->prepare("UPDATE jadwal SET sisa_tiket = sisa_tiket - ? WHERE id = ?");
    $stmt->execute([$jumlah_tiket, $jadwal_id]);
    
    $conn->commit();
    
    $_SESSION['success'] = "Pemesanan berhasil! ID: " . $conn->lastInsertId();
    header("Location: " . BASE_URL . "pages/jadwal.php");
    exit();
    
} catch (PDOException $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    $_SESSION['error'] = "Terjadi kesalahan sistem. Silakan coba lagi.";
    error_log("Database error: " . $e->getMessage());
    header("Location: " . BASE_URL . "pages/pesan_tiket.php?jadwal_id=".$jadwal_id);
    exit();
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    $_SESSION['error'] = $e->getMessage();
    header("Location: " . BASE_URL . "pages/pesan_tiket.php?jadwal_id=".$jadwal_id);
    exit();
}
?>

