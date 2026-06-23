<?php
session_start();
include '../db.php';

$id_mitra = isset($_GET['id']) ? $_GET['id'] : 0;

// Ambil data mitra yang baru mendaftar
$query = $conn->query("SELECT nama_mitra, paket FROM mitra WHERE id = '$id_mitra'");
$mitra = $query->fetch_assoc();

if (!$mitra) die("Data mitra tidak ditemukan.");

// Tentukan harga
$harga = 0;
if ($mitra['paket'] == 'Growth') $harga = 99000;
if ($mitra['paket'] == 'Scale') $harga = 249000;

// Proses saat tombol konfirmasi diklik
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Simulasi persetujuan admin: Langsung aktifkan akun
    $update = $conn->query("UPDATE mitra SET status_akun = 'Aktif' WHERE id = '$id_mitra'");
    if ($update) {
        echo "<script>alert('Pembayaran Berhasil! Akun Anda telah diaktifkan.'); window.location.href='login.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Pembayaran Berlangganan - FasilBook</title>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card p-4 shadow border-0 mx-auto" style="max-width: 500px;">
            <h4 class="text-center text-success mb-2">Aktivasi Paket <?php echo $mitra['paket']; ?></h4>
            <p class="text-center text-muted small mb-4">Halo, <?php echo htmlspecialchars($mitra['nama_mitra']); ?>. Selesaikan pembayaran untuk mengaktifkan akun Anda.</p>
            
            <div class="bg-white border rounded p-3 mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Biaya Langganan (1 Bulan)</span>
                    <span class="fw-bold">Rp <?php echo number_format($harga, 0, ',', '.'); ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <h5 class="mb-0">Total Dibayar</h5>
                    <h5 class="text-success fw-bold mb-0">Rp <?php echo number_format($harga, 0, ',', '.'); ?></h5>
                </div>
            </div>

            <!-- TAMPILAN QRIS PUSAT FASILBOOK -->
            <div class="border p-4 my-3 bg-white text-center rounded shadow-sm">
                <h6 class="mb-3 text-primary fw-bold">Scan QRIS Sistem Web</h6>
                <img src="../img/qris_dummy.png" alt="QRIS FasilBook" class="img-fluid border p-2 rounded mb-2" style="max-width: 200px;">
                <p class="text-muted mt-2 small">Sistem pembayaran langganan resmi FasilBook.</p>
            </div>

            <form action="" method="POST">
                <button type="submit" class="btn btn-success w-100 py-3 fw-bold">Saya Sudah Transfer (Simulasi Aktif)</button>
            </form>
        </div>
    </div>
</body>
</html>