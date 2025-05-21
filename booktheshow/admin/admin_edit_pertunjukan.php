<?php
require_once '../includes/koneksi.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_manage.php");
    exit();
}

$id = (int)$_GET['id'];
$pertunjukan = $conn->prepare("SELECT * FROM pertunjukan WHERE id = ?");
$pertunjukan->execute([$id]);
$pertunjukan = $pertunjukan->fetch(PDO::FETCH_ASSOC);

if (!$pertunjukan) {
    $_SESSION['error'] = "Pertunjukan tidak ditemukan";
    header("Location: admin_manage.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = clean_input($_POST['judul']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $durasi = (int)$_POST['durasi'];
    $gambar = $pertunjukan['gambar'];

    // Handle file upload - REMOVE THE DUPLICATE BLOCK
    if ($_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            $_SESSION['error'] = "Hanya file JPG/PNG/GIF yang diizinkan!";
            header("Location: admin_edit_pertunjukan.php?id=$id");
            exit();
        }

        if ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = "Ukuran file maksimal 2MB!";
            header("Location: admin_edit_pertunjukan.php?id=$id");
            exit();
        }

        $new_filename = uniqid('poster_') . '.' . $ext;
        // ===== END LANGKAH 6 ===== //

        // Hapus gambar lama jika bukan default
        if ($gambar !== 'default-show.png') {
            unlink("../uploads/$gambar");
        }

        move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/$new_filename");
        $gambar = $new_filename;
    }        

    try {
        $stmt = $conn->prepare("UPDATE pertunjukan SET judul = ?, deskripsi = ?, durasi = ?, gambar = ? WHERE id = ?");
        $stmt->execute([$judul, $deskripsi, $durasi, $gambar, $id]);
        $_SESSION['success'] = "Pertunjukan berhasil diperbarui!";
        header("Location: admin_manage.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Gagal update: ".$e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Pertunjukan - BookTheShow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="admin-container">
        <h1>Edit Pertunjukan</h1>
        
        <form method="POST" enctype="multipart/form-data" class="form-card">
            <div class="form-group">
                <label for="judul">Judul:</label>
                <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($pertunjukan['judul']) ?>" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi" rows="5" required><?= htmlspecialchars($pertunjukan['deskripsi']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="durasi">Durasi (menit):</label>
                <input type="number" id="durasi" name="durasi" min="1" value="<?= $pertunjukan['durasi'] ?>" required>
            </div>
            <div class="form-group">
                <label>Poster Saat Ini:</label>
                <img src="../uploads/<?= htmlspecialchars($pertunjukan['gambar']) ?>" alt="Poster" style="max-width: 200px; display: block; margin: 10px 0;">
                <label for="gambar">Ganti Poster:</label>
                <input type="file" id="gambar" name="gambar" accept="image/*">
            </div>
            <button type="submit" class="btn">Simpan Perubahan</button>
            <a href="admin_manage.php" class="btn-cancel">Batal</a>
        </form>
    </div>
</body>
</html>

