<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User tidak login']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['nama']) || !isset($data['mode']) || !isset($data['pembayaran']) || !isset($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$userId = $_SESSION['user_id'];
$nama = trim($data['nama']);
$mode = $data['mode'];
$pembayaran = $data['pembayaran'];
$items = $data['items'];
$totalAmount = $data['total'];

if (empty($nama) || empty($mode) || empty($pembayaran) || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

$conn = getDBConnection();

// Start transaction
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, order_mode, payment_method, total_amount, status, order_date) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("isssd", $userId, $nama, $mode, $pembayaran, $totalAmount);
    
    if (!$stmt->execute()) {
        throw new Exception('Gagal menyimpan pesanan: ' . $stmt->error);
    }
    
    $orderId = $conn->insert_id;
    $stmt->close();
    
    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, quantity, price) VALUES (?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $stmt->bind_param("isid", $orderId, $item['name'], $item['qty'], $item['price']);
        if (!$stmt->execute()) {
            throw new Exception('Gagal menyimpan item pesanan');
        }
    }
    
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Pesanan berhasil disimpan',
        'orderId' => $orderId
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>