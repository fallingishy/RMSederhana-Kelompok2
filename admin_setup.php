<?php
require_once 'config/database.php';

$success = '';
$error = '';

// Check if admin already exists
$conn = getDBConnection();
$result = $conn->query("SELECT COUNT(*) as count FROM admins");
$row = $result->fetch_assoc();
$adminExists = $row['count'] > 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$adminExists) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $nama = trim($_POST['nama']);
    
    if (empty($email) || empty($password) || empty($nama)) {
        $error = 'Semua field harus diisi!';
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert admin
        $stmt = $conn->prepare("INSERT INTO admins (email, password, nama) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashedPassword, $nama);
        
        if ($stmt->execute()) {
            $success = 'Admin berhasil dibuat! Silakan login.';
            $adminExists = true;
        } else {
            $error = 'Gagal membuat admin: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css">
    <title>Setup Admin - Rumah Makan Sederhana</title>
    <style>
        .setup-badge {
            display: inline-block;
            background: #1a1a1a;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
        .alert-success {
            background: #efe;
            color: #0a0;
            border: 1px solid #cfc;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <span class="setup-badge"><i class="ri-settings-3-line"></i> SETUP</span>
                <h1>Setup Admin</h1>
                <p>Buat akun admin untuk pertama kali</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                    <br><br>
                    <a href="admin_login.php" class="auth-btn" style="text-decoration: none; display: inline-block; margin-top: 0.5rem;">Login sebagai Admin</a>
                </div>
            <?php elseif ($adminExists): ?>
                <div class="alert alert-success">
                    Admin sudah ada. Silakan <a href="admin_login.php">login</a>.
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nama"><i class="ri-user-line"></i> Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="ri-mail-line"></i> Email Admin</label>
                        <input type="email" id="email" name="email" required placeholder="nama@gmail.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="ri-lock-line"></i> Password</label>
                        <input type="password" id="password" name="password" required placeholder="Buat password" minlength="6">
                        <small>Minimal 6 karakter</small>
                    </div>
                    
                    <button type="submit" class="auth-btn">Buat Akun Admin</button>
                </form>
            <?php endif; ?>
            
            <div class="auth-footer">
                <a href="index.php">Kembali ke halaman utama</a>
            </div>
        </div>
    </div>
</body>
</html>