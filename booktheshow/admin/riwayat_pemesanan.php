<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/koneksi.php';

try {
    $stmt = $conn->query("SELECT p.id, p.nama AS nama_pemesan, p.email, p.jumlah_tiket, p.tanggal_pesan,
                                j.tanggal AS tanggal_pertunjukan, j.waktu,
                                pr.judul AS nama_pertunjukan
                          FROM pemesanan p
                          JOIN jadwal j ON p.jadwal_id = j.id
                          JOIN pertunjukan pr ON p.pertunjukan_id = pr.id
                          ORDER BY p.tanggal_pesan DESC");
    $pemesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pemesanan Tiket</title>
    <link rel="stylesheet" href="../assets/css/style.css">
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
        }

        .admin-container {
            padding: 2rem;
        }

        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .back-link {
            display: inline-block;
            margin-top: 1rem;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>

    <div class="admin-container">
        <h1>Riwayat Pemesanan Tiket</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Nama Pertunjukan</th>
                    <th>Tanggal Pertunjukan</th>
                    <th>Waktu</th>
                    <th>Jumlah Tiket</th>
                    <th>Tanggal Pesan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pemesanan) > 0): ?>
                    <?php foreach ($pemesanan as $index => $data): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($data['nama_pemesan']) ?></td>
                            <td><?= htmlspecialchars($data['email']) ?></td>
                            <td><?= htmlspecialchars($data['nama_pertunjukan']) ?></td>
                            <td><?= htmlspecialchars($data['tanggal_pertunjukan']) ?></td>
                            <td><?= htmlspecialchars($data['waktu']) ?></td>
                            <td><?= htmlspecialchars($data['jumlah_tiket']) ?></td>
                            <td><?= htmlspecialchars($data['tanggal_pesan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">Belum ada pemesanan tiket.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="back-link">Kembali ke Dashboard</a>
    </div>
</body>
</html>