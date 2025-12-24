<?php
session_start();

// Hapus semua session admin
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_nama']);

session_destroy();

header('Location: admin_login.php');
exit;
?>
