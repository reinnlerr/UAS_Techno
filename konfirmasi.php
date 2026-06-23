<?php
session_start();
include '../db.php'; // Pastikan path ke db.php benar

// 1. Keamanan: Hanya mitra yang sudah login yang boleh mengakses ini
if (!isset($_SESSION['mitra_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Cek apakah ada ID pesanan yang dikirim
if (isset($_GET['id'])) {
    $id_pesanan = intval($_GET['id']);
    $mitra_id = $_SESSION['mitra_id'];

    // 3. Eksekusi Update (Security: Memastikan pesanan tersebut milik lapangan mitra yang sedang login)
    // Kita update status_pembayaran menjadi 'Lunas' 
    // DAN status_dana menjadi 'Siap Dicairkan' (agar Admin Pusat tahu dana ini sudah aman untuk ditransfer)
    $query = "UPDATE pesanan p 
              JOIN lapangan l ON p.lapangan_id = l.id 
              SET p.status_pembayaran = 'Lunas', 
                  p.status_dana = 'Siap Dicairkan' 
              WHERE p.id = '$id_pesanan' AND l.id_mitra = '$mitra_id'";
              
    if ($conn->query($query)) {
        echo "<script>
                alert('Konfirmasi berhasil! Pembayaran telah dinyatakan Lunas dan dana masuk ke antrian pencairan.');
                window.location.href='dashboard.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal mengkonfirmasi pembayaran: " . $conn->error . "');
                window.location.href='dashboard.php';
              </script>";
    }
} else {
    // Jika akses langsung ke file tanpa ID
    header("Location: dashboard.php");
}
?>