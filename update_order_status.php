<?php
session_start();
header('Content-Type: application/json');

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

require_once 'config/database.php';

$order_id = $_POST['order_id'] ?? null;
$status = $_POST['status'] ?? null;

// Validasi
$valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
if (!$order_id || !$status || !in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

$conn = getDBConnection();

// Update status pesanan
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status']);
}

$stmt->close();
$conn->close();
?>
