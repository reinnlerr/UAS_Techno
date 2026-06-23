<?php 
session_start();
if (!isset($_SESSION['mitra_id'])) {
    header("Location: login.php"); exit;
}
include '../db.php'; 
$mitra_id = $_SESSION['mitra_id']; 

// Ambil Data Mitra beserta Paketnya
$q_mitra = $conn->query("SELECT * FROM mitra WHERE id = '$mitra_id'");
$data_mitra = $q_mitra->fetch_assoc();
$paket = $data_mitra['paket'];
$komisi = $data_mitra['komisi_persen'];
$badge_color = ($paket == 'Scale') ? 'bg-primary' : (($paket == 'Growth') ? 'bg-info text-dark' : 'bg-secondary');

// HITUNG JUMLAH LAPANGAN & TENTUKAN BATAS (LIMIT)
$q_count = $conn->query("SELECT COUNT(id) as total FROM lapangan WHERE id_mitra = '$mitra_id'");
$total_lapangan = $q_count->fetch_assoc()['total'];
$batas_lapangan = ($paket == 'Scale') ? 10 : 1; // Starter & Growth maksimal 1, Scale maksimal 10
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Mitra - FasilBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f4f7f6; }
        .sidebar { background-color: #212529; min-height: 100vh; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); }
        .sidebar .nav-link.active { color: #2FB95D; font-weight: bold; }
        .locked-feature { opacity: 0.5; pointer-events: none; filter: grayscale(100%); position: relative;}
        .locked-overlay { position: absolute; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); display:flex; align-items:center; justify-content:center; flex-direction:column; z-index:10; border-radius: 8px;}
        .lapangan-img { width: 100%; height: 120px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;}
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-4">
                <h4 class="text-success mb-4"><i class="fa fa-futbol me-2"></i>FasilBook</h4>
                <div class="mb-4 text-center text-white bg-dark p-3 rounded border border-secondary">
                    <small class="d-block text-muted">Login sebagai:</small>
                    <span class="fw-bold d-block mb-2"><?php echo htmlspecialchars($data_mitra['nama_mitra']); ?></span>
                    <span class="badge <?php echo $badge_color; ?> w-100 py-2"><i class="fa fa-star me-1"></i> Paket: <?php echo $paket; ?></span>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="#"><i class="fa fa-home me-2"></i>Monitor Dasar</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.php"><i class="fa fa-globe me-2"></i>Web Utama</a></li>
                    <li class="nav-item mt-3"><a class="nav-link text-danger" href="logout.php"><i class="fa fa-power-off me-2"></i>Keluar</a></li>
                </ul>
            </div>

            <div class="col-md-10 p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Panel Pengelola Lapangan</h2>
                    <div class="text-end">
                        <small class="text-muted d-block">Potongan Komisi Transaksi Anda:</small>
                        <h4 class="text-danger fw-bold m-0"><?php echo $komisi; ?>% / Transaksi</h4>
                    </div>
                </div>

                <?php if($paket == 'Starter'): ?>
                <div class="alert alert-warning border-warning shadow-sm d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="fa fa-rocket me-2 text-warning"></i>Tingkatkan ke Paket Growth!</h5>
                        <span class="text-dark">Turunkan komisi menjadi <strong>6%</strong> dan buka akses ke Laporan Transaksi Lengkap hanya Rp99.000/Bulan.</span>
                    </div>
                    <button class="btn btn-warning fw-bold text-dark px-4 py-2 shadow-sm">Upgrade Sekarang</button>
                </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded mb-4">
                    <div class="card-header bg-white d-flex justify-content-between p-3 align-items-center border-bottom-0">
                        <h5 class="m-0 fw-bold"><i class="fa fa-th-large text-success me-2"></i>Data Lapangan (<?php echo $total_lapangan . '/' . $batas_lapangan; ?>)</h5>
                        
                        <?php if($total_lapangan < $batas_lapangan): ?>
                            <a href="tambah_lapangan.php" class="btn btn-sm btn-primary fw-bold shadow-sm"><i class="fa fa-plus me-1"></i>Tambah Lapangan Baru</a>
                        <?php else: ?>
                            <?php if($paket != 'Scale'): ?>
                                <span class="badge bg-warning text-dark px-3 py-2 border"><i class="fa fa-lock me-1"></i>Batas 1 Lapangan Tercapai. Upgrade ke Scale!</span>
                            <?php else: ?>
                                <span class="badge bg-secondary px-3 py-2"><i class="fa fa-check me-1"></i>Batas Maksimal Lapangan Terpenuhi</span>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-3">
                            <?php
                            $ql = $conn->query("SELECT * FROM lapangan WHERE id_mitra = '$mitra_id'");
                            if($ql->num_rows > 0):
                                while ($lap = $ql->fetch_assoc()):
                                    $img_path = !empty($lap['gambar']) ? '../'.$lap['gambar'] : 'https://via.placeholder.com/300x150/2FB95D/FFFFFF?text=FasilBook';
                            ?>
                                <div class="col-md-4">
                                    <div class="border p-3 rounded bg-light">
                                        <img src="<?php echo $img_path; ?>" class="lapangan-img" onerror="this.src='https://via.placeholder.com/300x150/2FB95D/FFFFFF?text=FasilBook'">
                                        <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($lap['nama_lapangan']); ?></h6>
                                        <div class="text-muted small mb-2"><i class="fa fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($lap['lokasi'] ?? '-'); ?></div>
                                        <span class="badge bg-success text-white w-100 py-2 fs-6">Rp <?php echo number_format($lap['harga_per_jam'],0,',','.'); ?> / jam</span>
                                    </div>
                                </div>
                            <?php 
                                endwhile; 
                            else:
                            ?>
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">Anda belum mendaftarkan lapangan. Silakan klik tombol "Tambah Lapangan Baru" di pojok kanan atas.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-7">
                        <div class="card border-0 shadow-sm rounded h-100">
                            <div class="card-header bg-dark text-white p-3 fw-bold">
                                <i class="fa fa-list me-2"></i>Reservasi Masuk (Fitur Dasar)
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped align-middle m-0">
                                    <thead><tr><th>Nama</th><th>Tgl</th><th>Status</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php
                                        $result = $conn->query("SELECT p.* FROM pesanan p JOIN lapangan l ON p.lapangan_id = l.id WHERE l.id_mitra = '$mitra_id' ORDER BY p.id DESC LIMIT 5");
                                        while ($row = $result->fetch_assoc()):
                                            $st = $row['status_pembayaran'];
                                            $bdg = ($st == 'Lunas') ? 'bg-success' : 'bg-warning text-dark';
                                        ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo $row['nama_pemesan']; ?></td>
                                            <td><?php echo $row['tanggal_booking']; ?></td>
                                            <td><span class="badge <?php echo $bdg; ?>"><?php echo $st; ?></span></td>
                                            <td>
                                                <?php if($st == 'Menunggu Konfirmasi') echo "<a href='konfirmasi.php?id={$row['id']}' class='btn btn-sm btn-success'>Konfirmasi</a>"; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card border-0 shadow-sm rounded h-100 <?php echo ($paket == 'Starter') ? 'locked-feature' : ''; ?>">
                            
                            <?php if($paket == 'Starter'): ?>
                                <div class="locked-overlay">
                                    <i class="fa fa-lock fa-3x text-secondary mb-2"></i>
                                    <h5 class="fw-bold text-dark">Fitur Dikunci</h5>
                                    <p class="text-muted small text-center px-3">Upgrade ke paket Growth atau Scale untuk melihat Analitik dan Laporan Keuangan.</p>
                                </div>
                            <?php endif; ?>

                            <div class="card-header bg-primary text-white p-3 fw-bold">
                                <i class="fa fa-chart-pie me-2"></i>Analitik & Laporan Keuangan
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <span class="text-muted">Total Pendapatan Bulan Ini</span>
                                    <span class="fw-bold text-success">Rp 4.500.000</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <span class="text-muted">Potongan Komisi FasilBook</span>
                                    <span class="fw-bold text-danger">- Rp 270.000</span>
                                </div>
                                
                                <a href="export_laporan.php" class="btn btn-outline-primary w-100">
                                    <i class="fa fa-download me-2"></i>Download Laporan Excel
                                </a>
                                
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>