<?php
session_start();
include '../db.php';

if (!isset($_SESSION['mitra_id'])) {
    header("Location: login.php");
    exit;
}

$mitra_id = $_SESSION['mitra_id'];

// Cek apakah mitra paket Scale
$q_mitra = $conn->query("SELECT paket FROM mitra WHERE id = '$mitra_id'");
$paket = $q_mitra->fetch_assoc()['paket'];

if ($paket != 'Scale') {
    echo "<script>alert('Fitur Manajemen Harga Promo hanya tersedia untuk paket Scale!'); window.location.href='dashboard.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_lapangan = intval($_POST['id_lapangan']);
    $harga_promo = $_POST['harga_promo'] !== '' ? intval($_POST['harga_promo']) : 'NULL';
    
    // Keamanan: Pastikan lapangan tersebut milik mitra yang sedang login
    $query = "UPDATE lapangan SET harga_promo = $harga_promo WHERE id = '$id_lapangan' AND id_mitra = '$mitra_id'";
    if ($conn->query($query)) {
        echo "<script>alert('Harga promo berhasil diperbarui!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui harga promo: " . $conn->error . "'); window.location.href='dashboard.php';</script>";
    }
} else {
    header("Location: dashboard.php");
}
?>
