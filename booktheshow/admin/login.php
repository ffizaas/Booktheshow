<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';

    if ($role === 'admin') {
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin'] = true;
            $_SESSION['username'] = $username;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Login admin gagal!";
        }
    } elseif ($role === 'user') {
        if (!empty($username)) {
            $_SESSION['user'] = $username;
            header("Location: /booktheshow/pages/index.php");
            exit();
        } else {
            $error = "Masukkan nama pengguna untuk login sebagai pengguna.";
        }
    } else {
        $error = "Pilih peran login.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - BookTheShow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; }
        input, select { width: 100%; padding: 0.5rem; }
        .btn { width: 100%; padding: 0.75rem; background-color: #333; color: #fff; border: none; border-radius: 5px; }
        .error { color: red; margin-top: 1rem; }
    </style>
</head>
<body>

<div class="login-container">
    <h1>Login BookTheShow</h1>
    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="role">Masuk sebagai:</label>
            <select name="role" id="role" required>
                <option value="">-- Pilih --</option>
                <option value="admin">Admin</option>
                <option value="user">Pengguna</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="username">Nama Pengguna:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group" id="password-group">
            <label for="password">Kata Sandi (Admin):</label>
            <input type="password" id="password" name="password">
        </div>

        <button type="submit" class="btn">Login</button>
    </form>
</div>

<script>
    // Tampilkan input password hanya untuk admin
    const roleSelect = document.getElementById('role');
    const passwordGroup = document.getElementById('password-group');

    roleSelect.addEventListener('change', function() {
        if (this.value === 'admin') {
            passwordGroup.style.display = 'block';
        } else {
            passwordGroup.style.display = 'none';
        }
    });

    // Default sembunyikan password saat load
    window.onload = () => {
        if (roleSelect.value !== 'admin') {
            passwordGroup.style.display = 'none';
        }
    };
</script>

</body>
</html>
