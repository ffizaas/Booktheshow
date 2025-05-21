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
$jadwal = $conn->prepare("
    SELECT j.*, p.judul 
    FROM jadwal j 
    JOIN pertunjukan p ON j.pertunjukan_id = p.id 
    WHERE j.id = ?
");
$jadwal->execute([$id]);
$jadwal = $jadwal->fetch(PDO::FETCH_ASSOC);

if (!$jadwal) {
    $_SESSION['error'] = "Jadwal tidak ditemukan";
    header("Location: admin_manage.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id' => $id,
        'pertunjukan_id' => (int)$_POST['pertunjukan_id'],
        'tanggal' => $_POST['tanggal'],
        'waktu' => $_POST['waktu'],
        'sisa_tiket' => (int)$_POST['sisa_tiket']
    ];
    
    try {
        $stmt = $conn->prepare("
            UPDATE jadwal 
            SET pertunjukan_id = :pertunjukan_id, 
                tanggal = :tanggal, 
                waktu = :waktu, 
                sisa_tiket = :sisa_tiket 
            WHERE id = :id
        ");
        $stmt->execute($data);
        $_SESSION['success'] = "Jadwal berhasil diperbarui!";
        header("Location: admin_manage.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Gagal update: ".$e->getMessage();
    }
}

$pertunjukans = $conn->query("SELECT * FROM pertunjukan")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Jadwal - BookTheShow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="admin-container">
        <h1>Edit Jadwal</h1>
        
        <form method="POST" class="form-card">
            <div class="form-group">
                <label for="pertunjukan_id">Pertunjukan:</label>
                <select id="pertunjukan_id" name="pertunjukan_id" required>
                    <?php foreach($pertunjukans as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $p['id'] == $jadwal['pertunjukan_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['judul']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" value="<?= $jadwal['tanggal'] ?>" required>
            </div>
            <div class="form-group">
                <label for="waktu">Waktu:</label>
                <input type="time" id="waktu" name="waktu" value="<?= $jadwal['waktu'] ?>" required>
            </div>
            <div class="form-group">
                <label for="sisa_tiket">Sisa Tiket:</label>
                <input type="number" id="sisa_tiket" name="sisa_tiket" min="0" value="<?= $jadwal['sisa_tiket'] ?>" required>
            </div>
            <button type="submit" class="btn">Simpan Perubahan</button>
            <a href="admin_manage.php" class="btn-cancel">Batal</a>
        </form>
    </div>
</body>
</html>