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
    $conn->query("UPDATE pesanan SET status_pembayaran = 'Dibatalkan (Refund)', status_dana = 'Ditahan' WHERE id = '$id_p'");
    echo "<script>alert('Pesanan di-refund. Dana dikembalikan ke pelanggan.'); window.location.href='dashboard.php';</script>";
}

// 3. ACTION: Cairkan Dana ke Mitra (hanya jika status_dana = 'Siap Dicairkan')
if (isset($_GET['cairkan_dana'])) {
    $id_p = intval($_GET['cairkan_dana']);
    $conn->query("UPDATE pesanan SET status_dana = 'Sudah Dicairkan' WHERE id = '$id_p' AND status_dana = 'Siap Dicairkan'");
    echo "<script>alert('Dana telah dicairkan ke mitra.'); window.location.href='dashboard.php';</script>";
}

// 4. ACTION: Approve Upgrade Paket
if (isset($_GET['approve_upgrade'])) {
    $id_upgrade = intval($_GET['approve_upgrade']);
    $q_upgrade = $conn->query("SELECT id_mitra FROM pengajuan_upgrade WHERE id = '$id_upgrade'");
    if ($q_upgrade->num_rows > 0) {
        $id_m = $q_upgrade->fetch_assoc()['id_mitra'];
        // Update mitra to Premium (Scale), komisi 4% dan aktifkan
        $conn->query("UPDATE mitra SET paket = 'Scale', komisi_persen = 4, status_akun = 'Aktif' WHERE id = '$id_m'");
        // Hapus pengajuan
        $conn->query("DELETE FROM pengajuan_upgrade WHERE id = '$id_upgrade'");
        echo "<script>alert('Mitra berhasil diupgrade ke Premium (Scale)!'); window.location.href='dashboard.php';</script>";
    }
}

// === DATA STATISTIK ===
$pendapatan = $conn->query("SELECT SUM((l.harga_per_jam * m.komisi_persen) / 100) as total FROM pesanan p JOIN lapangan l ON p.lapangan_id = l.id JOIN mitra m ON l.id_mitra = m.id WHERE p.status_pembayaran = 'Lunas'")->fetch_assoc()['total'] ?? 0;
$total_mitra = $conn->query("SELECT COUNT(id) as total FROM mitra WHERE status_akun = 'Aktif'")->fetch_assoc()['total'];
$total_pesanan = $conn->query("SELECT COUNT(id) as total FROM pesanan")->fetch_assoc()['total'];
$dana_tertahan = $conn->query("SELECT SUM(l.harga_per_jam) as total FROM pesanan p JOIN lapangan l ON p.lapangan_id = l.id WHERE p.status_dana = 'Ditahan' AND p.status_pembayaran = 'Menunggu Konfirmasi'")->fetch_assoc()['total'] ?? 0;
$dana_siap_cair = $conn->query("SELECT SUM(l.harga_per_jam - (l.harga_per_jam * m.komisi_persen / 100)) as total FROM pesanan p JOIN lapangan l ON p.lapangan_id = l.id JOIN mitra m ON l.id_mitra = m.id WHERE p.status_dana = 'Siap Dicairkan'")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Super Admin FasilBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; }
        .stat-card { border: none; border-radius: 12px; padding: 20px; }
        .stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; }
        .table td { vertical-align: middle; font-size: 0.9rem; }
        .badge { font-size: 0.78rem; padding: 5px 10px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark shadow-sm px-3">
        <span class="navbar-brand fw-bold text-success"><i class="fa fa-shield-alt"></i> FasilBook HQ — Super Admin</span>
        <div>
            <a href="../index.php" class="btn btn-sm btn-outline-secondary me-2"><i class="fa fa-globe me-1"></i>Web Utama</a>
            <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
        </div>
    </nav>

    <div class="container-fluid py-4 px-4">

        <!-- STATISTIK CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card bg-white shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3"><i class="fa fa-coins"></i></div>
                        <div>
                            <small class="text-muted d-block">Total Komisi FasilBook</small>
                            <h5 class="fw-bold text-success mb-0">Rp <?= number_format($pendapatan,0,',','.') ?></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3"><i class="fa fa-handshake"></i></div>
                        <div>
                            <small class="text-muted d-block">Mitra Aktif</small>
                            <h5 class="fw-bold text-primary mb-0"><?= $total_mitra ?> Mitra</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info me-3"><i class="fa fa-clipboard-list"></i></div>
                        <div>
                            <small class="text-muted d-block">Total Pesanan</small>
                            <h5 class="fw-bold text-info mb-0"><?= $total_pesanan ?> Pesanan</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card bg-white shadow-sm">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3"><i class="fa fa-wallet"></i></div>
                        <div>
                            <small class="text-muted d-block">Dana Siap Dicairkan</small>
                            <h5 class="fw-bold text-warning mb-0">Rp <?= number_format($dana_siap_cair,0,',','.') ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERMOHONAN MITRA BARU -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius:12px; overflow:hidden;">
            <div class="card-header bg-warning bg-opacity-75 fw-bold py-3"><i class="fa fa-user-plus me-2"></i>Permohonan Mitra Baru</div>
            <div class="table-responsive">
                <table class="table table-hover m-0">
                    <thead class="table-light"><tr><th>Nama Mitra</th><th>Paket</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php $req = $conn->query("SELECT * FROM mitra WHERE status_akun = 'Pending'");
                        if($req->num_rows > 0) {
                            while($m = $req->fetch_assoc()) {
                                echo "<tr><td class='fw-bold'>{$m['nama_mitra']}</td><td><span class='badge bg-secondary'>{$m['paket']}</span></td><td><a href='?aktifkan_mitra={$m['id']}' class='btn btn-sm btn-success' onclick=\"return confirm('Aktifkan mitra ini?')\"><i class='fa fa-check me-1'></i>Approve</a></td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' class='text-center text-muted py-3'>Tidak ada permohonan mitra baru.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- DAFTAR PENGAJUAN UPGRADE PAKET MITRA -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius:12px; overflow:hidden;">
            <div class="card-header bg-success text-white fw-bold py-3"><i class="fa fa-level-up-alt me-2"></i>Daftar Pengajuan Upgrade Paket Mitra</div>
            <div class="table-responsive">
                <table class="table table-hover m-0">
                    <thead class="table-light"><tr><th>Nama Mitra</th><th>Paket Asal</th><th>Status Pengajuan</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php 
                        $q_upg = $conn->query("SELECT u.id, u.paket_asal, u.status, m.nama_mitra FROM pengajuan_upgrade u JOIN mitra m ON u.id_mitra = m.id WHERE u.status = 'Pending Premium'");
                        if($q_upg && $q_upg->num_rows > 0) {
                            while($u = $q_upg->fetch_assoc()) {
                                echo "<tr>
                                    <td class='fw-bold'>{$u['nama_mitra']}</td>
                                    <td><span class='badge bg-secondary'>{$u['paket_asal']}</span></td>
                                    <td><span class='badge bg-warning text-dark'>{$u['status']}</span></td>
                                    <td><a href='?approve_upgrade={$u['id']}' class='btn btn-sm btn-success' onclick=\"return confirm('Setujui upgrade ke Premium untuk mitra ini?')\"><i class='fa fa-check me-1'></i>Setujui</a></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center text-muted py-3'>Tidak ada pengajuan upgrade ke Premium.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- PEMANTAUAN SEMUA PESANAN & REFUND -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius:12px; overflow:hidden;">
            <div class="card-header bg-danger text-white fw-bold py-3"><i class="fa fa-eye me-2"></i>Pemantauan Semua Pesanan & Refund</div>
            <div class="table-responsive">
                <table class="table table-hover m-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Lapangan</th>
                            <th>Mitra Pemilik</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Harga Sewa</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $all = $conn->query("SELECT p.id, p.nama_pemesan, p.tanggal_booking, p.jam_mulai, p.status_pembayaran, 
                                                    l.nama_lapangan, l.harga_per_jam, 
                                                    m.nama_mitra 
                                             FROM pesanan p 
                                             JOIN lapangan l ON p.lapangan_id = l.id 
                                             JOIN mitra m ON l.id_mitra = m.id 
                                             ORDER BY p.id DESC");
                        while($a = $all->fetch_assoc()) {
                            // Badge warna berdasarkan status
                            if ($a['status_pembayaran'] == 'Lunas') {
                                $badge = 'bg-success';
                            } elseif ($a['status_pembayaran'] == 'Menunggu Konfirmasi') {
                                $badge = 'bg-warning text-dark';
                            } else {
                                $badge = 'bg-danger';
                            }
                            
                            echo "<tr>
                                <td class='fw-bold'>#{$a['id']}</td>
                                <td>{$a['nama_pemesan']}</td>
                                <td><span class='fw-semibold'>{$a['nama_lapangan']}</span></td>
                                <td><small class='text-muted'>{$a['nama_mitra']}</small></td>
                                <td>" . date('d M Y', strtotime($a['tanggal_booking'])) . "</td>
                                <td>" . substr($a['jam_mulai'], 0, 5) . " WIB</td>
                                <td>Rp " . number_format($a['harga_per_jam'],0,',','.') . "</td>
                                <td><span class='badge {$badge}'>{$a['status_pembayaran']}</span></td>
                                <td>";
                            if ($a['status_pembayaran'] != 'Dibatalkan (Refund)') {
                                echo "<a href='?refund_pesanan={$a['id']}' class='btn btn-sm btn-outline-danger' onclick=\"return confirm('Yakin refund pesanan #{$a['id']}?')\"><i class='fa fa-undo me-1'></i>Refund</a>";
                            } else {
                                echo "<span class='text-muted small'>Sudah Refund</span>";
                            }
                            echo "</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MANAJEMEN PENCAIRAN DANA (ESCROW) -->
        <div class="card shadow-sm border-0" style="border-radius:12px; overflow:hidden;">
            <div class="card-header bg-info text-white fw-bold py-3"><i class="fa fa-money-check-alt me-2"></i>Manajemen Pencairan Dana (Escrow)</div>
            <div class="table-responsive">
                <table class="table table-hover m-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Lapangan</th>
                            <th>Mitra Pemilik</th>
                            <th>Tgl Booking</th>
                            <th>Dana ke Mitra</th>
                            <th>Komisi FB</th>
                            <th>Status Dana</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $q = $conn->query("SELECT p.id, p.nama_pemesan, p.tanggal_booking, p.status_dana, 
                                                        l.nama_lapangan, l.harga_per_jam, 
                                                        m.nama_mitra, m.komisi_persen 
                                                FROM pesanan p 
                                                JOIN lapangan l ON p.lapangan_id = l.id 
                                                JOIN mitra m ON l.id_mitra = m.id 
                                                WHERE p.status_pembayaran = 'Lunas'
                                                ORDER BY 
                                                    CASE p.status_dana 
                                                        WHEN 'Siap Dicairkan' THEN 1 
                                                        WHEN 'Ditahan' THEN 2 
                                                        WHEN 'Sudah Dicairkan' THEN 3 
                                                    END, p.id DESC");
                        while($d = $q->fetch_assoc()) {
                            $komisi_amount = ($d['harga_per_jam'] * $d['komisi_persen']) / 100;
                            $dana_mitra = $d['harga_per_jam'] - $komisi_amount;
                            
                            // 3 badge warna: Ditahan=kuning, Siap Dicairkan=biru, Sudah Dicairkan=hijau
                            if ($d['status_dana'] == 'Siap Dicairkan') {
                                $badge_dana = 'bg-info text-white';
                            } elseif ($d['status_dana'] == 'Sudah Dicairkan') {
                                $badge_dana = 'bg-success';
                            } else {
                                $badge_dana = 'bg-warning text-dark';
                            }
                            
                            echo "<tr>
                                <td class='fw-bold'>#{$d['id']}</td>
                                <td>{$d['nama_pemesan']}</td>
                                <td><span class='fw-semibold'>{$d['nama_lapangan']}</span></td>
                                <td><small class='text-muted'>{$d['nama_mitra']}</small></td>
                                <td>" . date('d M Y', strtotime($d['tanggal_booking'])) . "</td>
                                <td class='fw-bold text-success'>Rp " . number_format($dana_mitra,0,',','.') . "</td>
                                <td class='text-danger'>Rp " . number_format($komisi_amount,0,',','.') . " <small>({$d['komisi_persen']}%)</small></td>
                                <td><span class='badge {$badge_dana}'>{$d['status_dana']}</span></td>
                                <td>";
                            if ($d['status_dana'] == 'Siap Dicairkan') {
                                echo "<a href='?cairkan_dana={$d['id']}' class='btn btn-sm btn-info text-white' onclick=\"return confirm('Cairkan dana ke {$d['nama_mitra']}?')\"><i class='fa fa-paper-plane me-1'></i>Cairkan</a>";
                            } elseif ($d['status_dana'] == 'Sudah Dicairkan') {
                                echo "<span class='text-success small'><i class='fa fa-check-circle me-1'></i>Selesai</span>";
                            } else {
                                echo "<span class='text-muted small'>Menunggu Konfirmasi Mitra</span>";
                            }
                            echo "</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>