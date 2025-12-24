<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['contact_id'])) {
    echo json_encode(['success' => false, 'message' => 'Contact ID tidak ditemukan']);
    exit;
}

$contact_id = intval($_GET['contact_id']);
$conn = getDBConnection();

$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->bind_param("i", $contact_id);
$stmt->execute();
$result = $stmt->get_result();

if ($contact = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'contact' => $contact
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Pesan tidak ditemukan']);
}

$stmt->close();
$conn->close();
?>