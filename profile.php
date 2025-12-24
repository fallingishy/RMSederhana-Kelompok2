<?php
session_start();

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$error = '';
$success = '';
$user = null;

$conn = getDBConnection();

// Ambil data user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$orders = [];
$stmt = $conn->prepare("
    SELECT o.*, 
           GROUP_CONCAT(CONCAT(oi.item_name, ' (', oi.quantity, 'x)') SEPARATOR ', ') as items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['logout'])) {
        // Logout
        session_destroy();
        header('Location: login.php');
        exit;
    } else {
        // Update profil
        $nama = trim($_POST['nama']);
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $email = trim($_POST['email']);
        
        if (empty($email)) {
            $error = 'Email harus diisi!';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid!';
        } else {
            // Cek apakah email sudah digunakan user lain
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'Email sudah digunakan!';
            } else {
                // Update data
                $stmt = $conn->prepare("UPDATE users SET nama = ?, jenis_kelamin = ?, tanggal_lahir = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $nama, $jenis_kelamin, $tanggal_lahir, $email, $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    $success = 'Profil berhasil diperbarui!';
                    $_SESSION['user_email'] = $email;
                    
                    // Refresh data user
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                } else {
                    $error = 'Terjadi kesalahan. Silakan coba lagi.';
                }
            }
            $stmt->close();
        }
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
    <title>Profil - Rumah Makan Sederhana</title>
</head>
<body>
    <div class="profile-container">
        <div class="profile-box">
            <a href="index.php" class="back-link">
                <i class="ri-arrow-left-line"></i> Kembali ke Beranda
            </a>
            
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="ri-user-line"></i>
                </div>
                <h1>Profil Saya</h1>
                <p>Kelola informasi profil Anda</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama"><i class="ri-user-line"></i> Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama'] ?? '') ?>" placeholder="Masukkan nama lengkap">
                </div>
                
                <div class="form-group">
                    <label for="jenis_kelamin"><i class="ri-user-3-line"></i> Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin">
                        <option value="">Pilih jenis kelamin</option>
                        <option value="Laki-laki" <?= ($user['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= ($user['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="tanggal_lahir"><i class="ri-calendar-line"></i> Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?= htmlspecialchars($user['tanggal_lahir'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="ri-mail-line"></i> Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <div class="profile-actions">
                    <button type="submit" class="profile-btn profile-btn-primary">
                        <i class="ri-save-line"></i> Simpan Perubahan
                    </button>
                    <button type="submit" name="logout" class="profile-btn profile-btn-danger">
                        <i class="ri-logout-box-line"></i> Keluar
                    </button>
                </div>
            </form>
            
            <!-- Tambah section riwayat pesanan -->
            <div class="order-history">
                <h2><i class="ri-shopping-bag-line"></i> Riwayat Pesanan</h2>
                
                <?php if (empty($orders)): ?>
                    <div class="order-empty">
                        <i class="ri-inbox-line"></i>
                        <p>Belum ada riwayat pesanan</p>
                    </div>
                <?php else: ?>
                    <div class="order-list">
                        <?php 
                        $orderNumber = count($orders);
                        foreach ($orders as $order): 
                        ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-id">
                                        <i class="ri-file-list-line"></i>
                                        <span>Pesanan #<?= $orderNumber ?></span>
                                    </div>
                                    <span class="order-status status-<?= strtolower($order['status']) ?>">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </div>
                                
                                <div class="order-info">
                                    <div class="order-detail">
                                        <i class="ri-calendar-line"></i>
                                        <span><?= date('d M Y H:i', strtotime($order['order_date'])) ?></span>
                                    </div>
                                    <div class="order-detail">
                                        <i class="ri-restaurant-line"></i>
                                        <span><?= htmlspecialchars($order['items']) ?></span>
                                    </div>
                                    <div class="order-detail">
                                        <i class="ri-money-dollar-circle-line"></i>
                                        <span>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                                    </div>
                                    <div class="order-detail">
                                        <i class="ri-bank-card-line"></i>
                                        <span><?= htmlspecialchars($order['payment_method']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php 
                        $orderNumber--;
                        endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>