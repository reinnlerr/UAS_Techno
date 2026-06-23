<?php
session_start();
include '../db.php'; 

// Pastikan yang mengakses ini sudah login sebagai mitra
if (!isset($_SESSION['mitra_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_pesanan = $_GET['id'];
    $mitra_id = $_SESSION['mitra_id'];

    // Update status menjadi Lunas dengan keamanan memastikan pesanan itu milik lapangan mitra tsb
    $query = "UPDATE pesanan p 
              JOIN lapangan l ON p.lapangan_id = l.id 
              SET p.status_pembayaran = 'Lunas' 
              WHERE p.id = '$id_pesanan' AND l.id_mitra = '$mitra_id'";
              
    if ($conn->query($query)) {
        echo "<script>
                alert('Pembayaran berhasil dikonfirmasi menjadi Lunas!');
                window.location.href='dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal mengkonfirmasi pembayaran: " . $conn->error . "');
                window.location.href='dashboard.php';
              </script>";
    }
}
?>