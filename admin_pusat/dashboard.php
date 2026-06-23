<?php
session_start();
if (!isset($_SESSION['superadmin'])) { header("Location: login.php"); exit; }
include '../db.php';

// 1. ACTION: Aktifkan Mitra (Approve)
if (isset($_GET['aktifkan_mitra'])) {
    $id_m = intval($_GET['aktifkan_mitra']);
    $conn->query("UPDATE mitra SET status_akun = 'Aktif' WHERE id = '$id_m'");
    echo "<script>alert('Mitra berhasil diaktifkan!'); window.location.href='dashboard.php';</script>";
}

// 2. ACTION: Batal & Refund
if (isset($_GET['refund_pesanan'])) {
    $id_p = intval($_GET['refund_pesanan']);
    $conn->query("UPDATE pesanan SET status_pembayaran = 'Dibatalkan (Refund)' WHERE id = '$id_p'");
    echo "<script>alert('Pesanan di-refund. Dana dikembalikan ke pelanggan.'); window.location.href='dashboard.php';</script>";
}

// 3. ACTION: Cairkan Dana ke Mitra
if (isset($_GET['cairkan_dana'])) {
    $id_p = intval($_GET['cairkan_dana']);
    $conn->query("UPDATE pesanan SET status_dana = 'Sudah Dicairkan' WHERE id = '$id_p'");
    echo "<script>alert('Dana telah dicairkan ke mitra.'); window.location.href='dashboard.php';</script>";
}

// Data Statistik
$pendapatan = $conn->query("SELECT SUM((l.harga_per_jam * m.komisi_persen) / 100) as total FROM pesanan p JOIN lapangan l ON p.lapangan_id = l.id JOIN mitra m ON l.id_mitra = m.id WHERE p.status_pembayaran = 'Lunas'")->fetch_assoc()['total'] ?? 0;
$total_mitra = $conn->query("SELECT COUNT(id) as total FROM mitra WHERE status_akun = 'Aktif'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Super Admin FasilBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark shadow-sm px-3">
        <span class="navbar-brand fw-bold text-success"><i class="fa fa-shield-alt"></i> FasilBook HQ</span>
        <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
    </nav>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-6"><div class="card p-3 border-0 shadow-sm border-start border-success border-5"><h6>Total Komisi</h6><h3 class="text-success">Rp <?= number_format($pendapatan,0,',','.') ?></h3></div></div>
            <div class="col-md-6"><div class="card p-3 border-0 shadow-sm border-start border-primary border-5"><h6>Mitra Aktif</h6><h3 class="text-primary"><?= $total_mitra ?> Mitra</h3></div></div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-warning fw-bold">Permohonan Mitra Baru</div>
            <table class="table m-0">
                <thead><tr><th>Nama Mitra</th><th>Pakej</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php $req = $conn->query("SELECT * FROM mitra WHERE status_akun = 'Pending'");
                    while($m = $req->fetch_assoc()) {
                        echo "<tr><td>{$m['nama_mitra']}</td><td>{$m['paket']}</td><td><a href='?aktifkan_mitra={$m['id']}' class='btn btn-sm btn-success'>Approve</a></td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-danger text-white fw-bold">Pemantauan & Refund (Keadilan Pelanggan)</div>
            <table class="table m-0">
                <thead><tr><th>ID</th><th>Pelanggan</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php $all = $conn->query("SELECT p.id, p.nama_pemesan, p.status_pembayaran FROM pesanan p");
                    while($a = $all->fetch_assoc()) {
                        echo "<tr><td>#{$a['id']}</td><td>{$a['nama_pemesan']}</td><td>{$a['status_pembayaran']}</td>
                        <td>".($a['status_pembayaran'] != 'Dibatalkan (Refund)' ? "<a href='?refund_pesanan={$a['id']}' class='btn btn-sm btn-outline-danger'>Batal & Refund</a>" : "-")."</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white fw-bold">Manajemen Pencairan Dana (Escrow)</div>
            <table class="table m-0">
                <thead><tr><th>ID</th><th>Mitra</th><th>Dana ke Mitra</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php $q = $conn->query("SELECT p.id, p.status_dana, m.nama_mitra, l.harga_per_jam, m.komisi_persen 
                                           FROM pesanan p JOIN lapangan l ON p.lapangan_id = l.id 
                                           JOIN mitra m ON l.id_mitra = m.id WHERE p.status_pembayaran = 'Lunas'");
                    while($d = $q->fetch_assoc()) {
                        $dana = $d['harga_per_jam'] - (($d['harga_per_jam'] * $d['komisi_persen']) / 100);
                        echo "<tr><td>#{$d['id']}</td><td>{$d['nama_mitra']}</td><td>Rp ".number_format($dana,0,',','.')."</td>
                        <td><span class='badge ".($d['status_dana']=='Ditahan'?'bg-warning':'bg-info')."'>{$d['status_dana']}</span></td>
                        <td>".($d['status_dana']=='Ditahan' ? "<a href='?cairkan_dana={$d['id']}' class='btn btn-sm btn-info text-white'>Cairkan</a>" : "Selesai")."</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html><?php
session_start();
include '../db.php';
if (!isset($_SESSION['superadmin'])) { header("Location: login.php"); exit; }

// LOGIKA ACTION
if (isset($_GET['verifikasi'])) { // Admin terima dana dari pelanggan
    $conn->query("UPDATE pesanan SET status_pembayaran = 'Siap_Konfirmasi_Mitra' WHERE id = ".intval($_GET['verifikasi']));
}
if (isset($_GET['cairkan_dana'])) { // Admin transfer dana ke mitra
    $conn->query("UPDATE pesanan SET status_dana = 'Dicairkan' WHERE id = ".intval($_GET['cairkan_dana']));
}
?>
<!-- Gunakan template HTML Admin yang sebelumnya, pastikan tabel berisi 3 bagian: Verifikasi, Payout, dan Refund -->