<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $stmt = $conn->prepare("DELETE FROM alternatif WHERE id_alternatif = ?");
        $stmt->execute([$id]);
        
        $_SESSION['message'] = "Data berhasil dihapus!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

header('Location: index.php');
exit;
?>
