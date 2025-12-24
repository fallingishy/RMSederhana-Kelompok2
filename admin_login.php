<?php
session_start();

// Redirect jika sudah login sebagai admin
if (isset($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'config/database.php';
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi!';
    } else {
        $conn = getDBConnection();
        
        // Cari admin berdasarkan email
        $stmt = $conn->prepare("SELECT id, email, password, nama FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Verifikasi password
            if (password_verify($password, $admin['password'])) {
                // Set session
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_nama'] = $admin['nama'];
                
                header('Location: admin_dashboard.php');
                exit;
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Email admin tidak terdaftar! Silakan setup admin terlebih dahulu.';
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css">
    <title>Admin Login - Rumah Makan Sederhana</title>
    <style>
        .admin-badge {
            display: inline-block;
            background: #1a1a1a;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        /* Add alert styling */
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
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <span class="admin-badge"><i class="ri-shield-star-line"></i> ADMIN</span>
                <h1>Admin Login</h1>
                <p>Masuk ke Dashboard Admin</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email"><i class="ri-mail-line"></i> Email Admin</label>
                    <input type="email" id="email" name="email" required placeholder="nama@gmail.com">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="ri-lock-line"></i> Password</label>
                    <input type="password" id="password" name="password" required placeholder="Masukkan password">
                </div>
                
                <button type="submit" class="auth-btn">Login sebagai Admin</button>
            </form>
            
            <div class="auth-footer">
                Belum ada akun admin? <a href="admin_setup.php">Setup Admin</a><br>
                Bukan admin? <a href="login.php">Login sebagai customer</a>
            </div>
        </div>
    </div>
</body>
</html>