<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_id = intval($_POST['contact_id'] ?? 0);
    
    if ($contact_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Contact ID tidak valid']);
        exit;
    }
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $contact_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pesan berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus pesan']);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
}
?>
