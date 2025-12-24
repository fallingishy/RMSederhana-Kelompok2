<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

error_log("[v0] Contact form submission received");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');
    
    error_log("[v0] Data received - Nama: $nama, Email: $email");
    
    // Validasi input
    if (empty($nama) || empty($email) || empty($pesan)) {
        error_log("[v0] Validation failed - empty fields");
        echo json_encode(['success' => false, 'message' => 'Semua field harus diisi']);
        exit;
    }
    
    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("[v0] Validation failed - invalid email");
        echo json_encode(['success' => false, 'message' => 'Email tidak valid']);
        exit;
    }
    
    try {
        $conn = getDBConnection();
        error_log("[v0] Database connection established");
        
        // Simpan pesan kontak ke database
        $stmt = $conn->prepare("INSERT INTO contacts (nama, email, pesan, status, created_at) VALUES (?, ?, ?, 'unread', NOW())");
        $stmt->bind_param("sss", $nama, $email, $pesan);
        
        if ($stmt->execute()) {
            error_log("[v0] Contact saved successfully with ID: " . $stmt->insert_id);
            echo json_encode(['success' => true, 'message' => 'Pesan Anda berhasil dikirim. Terima kasih!']);
        } else {
            error_log("[v0] Failed to execute statement: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Gagal mengirim pesan. Silakan coba lagi.']);
        }
        
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        error_log("[v0] Exception occurred: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
    }
} else {
    error_log("[v0] Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
}
?>