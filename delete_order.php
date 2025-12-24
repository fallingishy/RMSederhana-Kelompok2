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

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Order ID tidak valid']);
    exit;
}

$conn = getDBConnection();

// Mulai transaction
$conn->begin_transaction();

try {
    // Hapus order items terlebih dahulu
    $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
    
    // Hapus order
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil dihapus']);
} catch (Exception $e) {
    // Rollback jika ada error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus pesanan: ' . $e->getMessage()]);
}

$conn->close();
?>