<?php
require_once '../includes/koneksi.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tambah Pertunjukan
    if (isset($_POST['tambah_pertunjukan'])) {
        $judul = clean_input($_POST['judul']);
        $deskripsi = clean_input($_POST['deskripsi']);
        $durasi = (int)$_POST['durasi'];
        $gambar = 'default-show.png';
    
        // Handle file upload - REMOVE THE DUPLICATE BLOCK
        if ($_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed_ext)) {
            $_SESSION['error'] = "Hanya file JPG/PNG/GIF yang diizinkan!";
            header("Location: admin_manage.php");
            exit();
        }

        // Validasi ukuran file (2MB)
        if ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = "Ukuran file maksimal 2MB!";
            header("Location: admin_manage.php");
            exit();
        }

        // Generate nama file unik
        $gambar = uniqid('poster_') . '.' . $ext;
        // ===== END LANGKAH 6 ===== //

        move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/$gambar");
    }

        try {
            $stmt = $conn->prepare("INSERT INTO pertunjukan (judul, deskripsi, durasi, gambar) VALUES (?, ?, ?, ?)");
            $stmt->execute([$judul, $deskripsi, $durasi, $gambar]);
            $_SESSION['success'] = "Pertunjukan berhasil ditambahkan!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menambah pertunjukan: ".$e->getMessage();
        }
    }
    
    // Tambah Jadwal
    elseif (isset($_POST['tambah_jadwal'])) {
        $data = [
            'pertunjukan_id' => (int)$_POST['pertunjukan_id'],
            'tanggal' => $_POST['tanggal'],
            'waktu' => $_POST['waktu'],
            'sisa_tiket' => (int)$_POST['sisa_tiket']
        ];
        
        try {
            $stmt = $conn->prepare("INSERT INTO jadwal (pertunjukan_id, tanggal, waktu, sisa_tiket) VALUES (:pertunjukan_id, :tanggal, :waktu, :sisa_tiket)");
            $stmt->execute($data);
            $_SESSION['success'] = "Jadwal berhasil ditambahkan!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menambah jadwal: ".$e->getMessage();
        }
    }
    
    // Hapus Data
    elseif (isset($_POST['delete_id'])) {
        $table = clean_input($_POST['table']);
        $id = (int)$_POST['delete_id'];
        
        try {
            if ($table === 'pertunjukan') {
                // Cek dulu apakah ada jadwal terkait
                $stmt = $conn->prepare("SELECT gambar FROM pertunjukan WHERE id = ?");
                $stmt->execute([$id]);
                $gambar = $stmt->fetchColumn();
                
                if ($gambar !== 'default-show.png') {
                    unlink("../uploads/$gambar");
                }
                
                $conn->prepare("DELETE FROM pertunjukan WHERE id = ?")->execute([$id]);
            } else {
                $conn->prepare("DELETE FROM jadwal WHERE id = ?")->execute([$id]);
            }
            $_SESSION['success'] = "Data berhasil dihapus!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menghapus: ".$e->getMessage();
        }
    }
}

// Get all data
$pertunjukans = $conn->query("SELECT * FROM pertunjukan")->fetchAll(PDO::FETCH_ASSOC);
$jadwals = $conn->query("
    SELECT j.*, p.judul 
    FROM jadwal j 
    JOIN pertunjukan p ON j.pertunjukan_id = p.id
    ORDER BY j.tanggal DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Data - BookTheShow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    
    <div class="admin-container">
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Form Tambah Pertunjukan -->
        <form method="POST" enctype="multipart/form-data" class="form-card">
            <h2>Tambah Pertunjukan Baru</h2>
            <div class="form-group">
                <label for="judul">Judul:</label>
                <input type="text" id="judul" name="judul" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="durasi">Durasi (menit):</label>
                <input type="number" id="durasi" name="durasi" min="1" required>
            </div>
            <div class="form-group">
                <label for="gambar">Poster:</label>
                <input type="file" id="gambar" name="gambar" accept="image/*">
            </div>
            <button type="submit" name="tambah_pertunjukan" class="btn">Simpan Pertunjukan</button>
        </form>

        <!-- Form Tambah Jadwal -->
        <form method="POST" class="form-card">
            <h2>Tambah Jadwal Baru</h2>
            <div class="form-group">
                <label for="pertunjukan_id">Pertunjukan:</label>
                <select id="pertunjukan_id" name="pertunjukan_id" required>
                    <option value="">-- Pilih Pertunjukan --</option>
                    <?php foreach($pertunjukans as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['judul']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" required>
            </div>
            <div class="form-group">
                <label for="waktu">Waktu:</label>
                <input type="time" id="waktu" name="waktu" required>
            </div>
            <div class="form-group">
                <label for="sisa_tiket">Jumlah Tiket:</label>
                <input type="number" id="sisa_tiket" name="sisa_tiket" min="1" required>
            </div>
            <button type="submit" name="tambah_jadwal" class="btn">Simpan Jadwal</button>
        </form>

        <!-- Tabel Data -->
        <div class="data-tables">
            <h2>Daftar Pertunjukan</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Durasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pertunjukans as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['judul']) ?></td>
                        <td><?= $p['durasi'] ?> menit</td>
                        <td class="actions">
                            <a href="admin_edit_pertunjukan.php?id=<?= $p['id'] ?>" class="btn-edit">Edit</a>
                            <form method="POST" onsubmit="return confirm('Hapus pertunjukan ini?')">
                                <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="table" value="pertunjukan">
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Daftar Jadwal</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pertunjukan</th>
                        <th>Tanggal</th>
                        <th>Sisa Tiket</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($jadwals as $j): ?>
                    <tr>
                        <td><?= $j['id'] ?></td>
                        <td><?= htmlspecialchars($j['judul']) ?></td>
                        <td><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                        <td><?= $j['sisa_tiket'] ?></td>
                        <td class="actions">
                            <a href="edit_jadwal.php?id=<?= $j['id'] ?>" class="btn-edit">Edit</a>
                            <form method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                <input type="hidden" name="delete_id" value="<?= $j['id'] ?>">
                                <input type="hidden" name="table" value="jadwal">
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

