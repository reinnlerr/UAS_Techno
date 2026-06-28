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

// LOGIKA PENGAJUAN UPGRADE DARI DASHBOARD
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_upgrade'])) {
    if ($_POST['action_upgrade'] == 'premium') {
        // Cek apakah sudah ada pengajuan
        $cek = $conn->query("SELECT id FROM pengajuan_upgrade WHERE id_mitra = '$mitra_id' AND status = 'Pending Premium'");
        if ($cek->num_rows == 0) {
            $conn->query("INSERT INTO pengajuan_upgrade (id_mitra, paket_asal, status) VALUES ('$mitra_id', '$paket', 'Pending Premium')");
        }
        
        if (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1') {
            echo json_encode(['status' => 'success']);
            exit;
        }

        echo "<script>alert('Pengajuan Upgrade ke Premium berhasil dikirim! Silakan tunggu konfirmasi Super Admin.'); window.location.href='dashboard.php';</script>";
        exit;
    } elseif ($_POST['action_upgrade'] == 'normal') {
        // Melanjutkan alur pembayaran standar/growth asli
        if (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1') {
            echo json_encode(['status' => 'redirect', 'url' => 'pembayaran_mitra.php?id=' . $mitra_id]);
            exit;
        }
        header("Location: pembayaran_mitra.php?id=" . $mitra_id);
        exit;
    }
}

// HITUNG JUMLAH LAPANGAN & TENTUKAN BATAS (LIMIT)
$q_count = $conn->query("SELECT COUNT(id) as total FROM lapangan WHERE id_mitra = '$mitra_id'");
$total_lapangan = $q_count->fetch_assoc()['total'];
if ($paket == 'Scale') {
    $batas_lapangan = 5;
} elseif ($paket == 'Growth') {
    $batas_lapangan = 2;
} else {
    $batas_lapangan = 1;
}
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
                    <button type="button" class="btn btn-warning fw-bold text-dark px-4 py-2 shadow-sm btn-upgrade-dash" data-paket="Starter">Upgrade Sekarang</button>
                </div>
                <?php elseif($paket == 'Growth'): ?>
                <div class="alert alert-info border-info shadow-sm d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="fa fa-gem me-2 text-primary"></i>Tingkatkan ke Paket Premium!</h5>
                        <span class="text-dark">Turunkan komisi menjadi <strong>4%</strong>, batas 5 lapangan, dan fitur analitik maksimal!</span>
                    </div>
                    <button type="button" class="btn btn-info fw-bold text-dark px-4 py-2 shadow-sm btn-upgrade-dash" data-paket="Growth">Upgrade Sekarang</button>
                </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded mb-4">
                    <div class="card-header bg-white d-flex justify-content-between p-3 align-items-center border-bottom-0">
                        <h5 class="m-0 fw-bold"><i class="fa fa-th-large text-success me-2"></i>Data Lapangan (<?php echo $total_lapangan . '/' . $batas_lapangan; ?>)</h5>
                        
                        <?php if($total_lapangan < $batas_lapangan): ?>
                            <a href="tambah_lapangan.php" class="btn btn-sm btn-primary fw-bold shadow-sm"><i class="fa fa-plus me-1"></i>Tambah Lapangan Baru</a>
                        <?php else: ?>
                                <?php if($paket == 'Starter'): ?>
                                    <span class="badge bg-warning text-dark px-3 py-2 border"><i class="fa fa-lock me-1"></i>Batas 1 Lapangan Tercapai. Upgrade ke Growth!</span>
                                <?php elseif($paket == 'Growth'): ?>
                                    <span class="badge bg-warning text-dark px-3 py-2 border"><i class="fa fa-lock me-1"></i>Batas 2 Lapangan Tercapai. Upgrade ke Scale!</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary px-3 py-2"><i class="fa fa-check me-1"></i>Batas Maksimal (5) Terpenuhi</span>
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
                                        <?php if (!empty($lap['harga_promo'])): ?>
                                             <span class="badge bg-danger text-white w-100 py-2 fs-6 mb-2">Rp <?php echo number_format($lap['harga_promo'],0,',','.'); ?> / jam (Promo)</span>
                                             <div class="text-center mb-2"><small class="text-muted text-decoration-line-through">Normal: Rp <?php echo number_format($lap['harga_per_jam'],0,',','.'); ?></small></div>
                                         <?php else: ?>
                                             <span class="badge bg-success text-white w-100 py-2 fs-6">Rp <?php echo number_format($lap['harga_per_jam'],0,',','.'); ?> / jam</span>
                                         <?php endif; ?>

                                         <?php if ($paket == 'Scale'): ?>
                                             <form action="update_promo.php" method="POST" class="mt-3 border-top pt-2">
                                                 <input type="hidden" name="id_lapangan" value="<?php echo $lap['id']; ?>">
                                                 <label class="form-label small fw-bold text-muted mb-1"><i class="fa fa-tag text-warning"></i> Set Harga Promo</label>
                                                 <div class="input-group input-group-sm">
                                                     <input type="number" name="harga_promo" class="form-control" placeholder="Harga Promo" value="<?php echo htmlspecialchars($lap['harga_promo'] ?? ''); ?>">
                                                     <button class="btn btn-warning fw-bold text-dark" type="submit">Set</button>
                                                 </div>
                                                 <div class="text-center mt-1"><small class="text-muted" style="font-size: 10px;">Kosongkan & klik Set untuk hapus promo</small></div>
                                             </form>
                                         <?php endif; ?>
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
                                    <thead><tr><th>Nama</th><th>Lapangan</th><th>Tanggal & Jam</th><th>Status</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php
                                        $result = $conn->query("SELECT p.*, l.nama_lapangan FROM pesanan p JOIN lapangan l ON p.lapangan_id = l.id WHERE l.id_mitra = '$mitra_id' ORDER BY p.id DESC LIMIT 5");
                                        while ($row = $result->fetch_assoc()):
                                            $st = $row['status_pembayaran'];
                                            if ($st == 'Lunas') {
                                                $bdg = 'bg-success';
                                            } elseif ($st == 'Menunggu Konfirmasi') {
                                                $bdg = 'bg-warning text-dark';
                                            } else {
                                                $bdg = 'bg-danger';
                                            }
                                        ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo htmlspecialchars($row['nama_pemesan']); ?></td>
                                            <td><small><?php echo htmlspecialchars($row['nama_lapangan']); ?></small></td>
                                            <td><small><?php echo date('d/m/Y', strtotime($row['tanggal_booking'])) . ' ' . substr($row['jam_mulai'], 0, 5); ?></small></td>
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
                                <?php
                                // Ambil total pendapatan lunas
                                $q_finance = $conn->query("SELECT SUM(l.harga_per_jam) as total FROM pesanan p JOIN lapangan l ON p.lapangan_id = l.id WHERE l.id_mitra = '$mitra_id' AND p.status_pembayaran = 'Lunas'");
                                $total_kotor = $q_finance->fetch_assoc()['total'] ?? 0;
                                $potongan_fb = ($total_kotor * $komisi) / 100;
                                $bersih_mitra = $total_kotor - $potongan_fb;
                                ?>
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <span class="text-muted">Total Pendapatan (Kotor)</span>
                                    <span class="fw-bold text-success">Rp <?php echo number_format($total_kotor, 0, ',', '.'); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                    <span class="text-muted">Potongan Komisi FasilBook (<?php echo $komisi; ?>%)</span>
                                    <span class="fw-bold text-danger">- Rp <?php echo number_format($potongan_fb, 0, ',', '.'); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 border-bottom pb-2 bg-light p-2 rounded">
                                    <span class="fw-bold">Total Bersih Diterima</span>
                                    <span class="fw-bold text-primary">Rp <?php echo number_format($bersih_mitra, 0, ',', '.'); ?></span>
                                </div>
                                
                                <a href="export_laporan.php" class="btn btn-outline-primary w-100 mt-2">
                                    <i class="fa fa-download me-2"></i>Download Laporan Excel
                                </a>
                                
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Upgrade Dinamis -->
    <div class="modal fade" id="modalUpgradeDash" tabindex="-1" aria-labelledby="modalUpgradeDashLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-white">
                    <h5 class="modal-title fw-bold text-success" id="modalUpgradeDashLabel">Tawaran Spesial Premium!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fa fa-gem fa-3x text-warning mb-3"></i>
                    <h5 class="fw-bold mb-3">Tunggu Dulu!</h5>
                    <p id="modalDescDash" class="text-muted mb-4"></p>
                    <div class="bg-light p-3 rounded mb-4 text-start">
                        <h6 class="fw-bold text-primary mb-2">Keunggulan Paket Premium (Scale):</h6>
                        <ul class="small text-muted mb-0">
                            <li><i class="fa fa-check text-success me-2"></i>Batas maksimal 5 lapangan.</li>
                            <li><i class="fa fa-check text-success me-2"></i>Potongan komisi hanya 4%.</li>
                            <li><i class="fa fa-check text-success me-2"></i>Akses penuh Laporan Analitik & Keuangan.</li>
                            <li><i class="fa fa-check text-success me-2"></i>Bisa mengatur Harga Promo.</li>
                        </ul>
                    </div>
                    <form id="formUpgradeDash" class="d-flex flex-column gap-2">
                        <button type="button" id="btnPremiumAjax" class="btn btn-warning fw-bold text-dark w-100 py-2">
                            <i class="fa fa-rocket me-2"></i>Upgrade ke Premium
                        </button>
                        <button type="button" id="btnNormalAjax" class="btn btn-outline-secondary w-100 py-2">
                            Tetap Lanjut <span id="namaPaketLanjutDash"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnsUpgrade = document.querySelectorAll('.btn-upgrade-dash');
            if (btnsUpgrade.length > 0) {
                const modal = new bootstrap.Modal(document.getElementById('modalUpgradeDash'));
                const modalDesc = document.getElementById('modalDescDash');
                const namaPaketLanjut = document.getElementById('namaPaketLanjutDash');
                let currentBtnEl = null;

                btnsUpgrade.forEach(btn => {
                    btn.addEventListener('click', function() {
                        currentBtnEl = this;
                        const paket = this.getAttribute('data-paket');
                        
                        if (paket === 'Starter') {
                            modalDesc.innerHTML = "Anda saat ini berada di <strong>Paket Standar (Starter)</strong>.<br>Batasan: Maksimal hanya 1 lapangan dan tanpa laporan otomatis.";
                            namaPaketLanjut.innerText = "ke Growth";
                        } else if (paket === 'Growth') {
                            modalDesc.innerHTML = "Anda saat ini berada di <strong>Paket Growth</strong>.<br>Batasan: Fitur menengah, kuota lapangan terbatas (maks 2), dan potongan 6%.";
                            namaPaketLanjut.innerText = "Perpanjang Growth";
                        }
                        
                        modal.show();
                    });
                });

                document.getElementById('btnPremiumAjax').addEventListener('click', function() {
                    const btn = this;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Memproses...';
                    btn.disabled = true;

                    const formData = new FormData();
                    formData.append('action_upgrade', 'premium');
                    formData.append('is_ajax', '1');

                    fetch('dashboard.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            modal.hide();
                            alert('Pengajuan Upgrade ke Premium berhasil dikirim! Silakan tunggu konfirmasi Super Admin.');
                            // Update UI langsung tanpa refresh
                            if (currentBtnEl) {
                                currentBtnEl.innerText = "Menunggu Konfirmasi";
                                currentBtnEl.classList.remove('btn-warning', 'btn-info');
                                currentBtnEl.classList.add('btn-secondary');
                                currentBtnEl.disabled = true;
                            }
                        }
                        btn.innerHTML = '<i class="fa fa-rocket me-2"></i>Upgrade ke Premium';
                        btn.disabled = false;
                    })
                    .catch(err => {
                        alert('Terjadi kesalahan koneksi.');
                        btn.innerHTML = '<i class="fa fa-rocket me-2"></i>Upgrade ke Premium';
                        btn.disabled = false;
                    });
                });

                document.getElementById('btnNormalAjax').addEventListener('click', function() {
                    const btn = this;
                    btn.disabled = true;
                    btn.innerText = 'Memproses...';

                    const formData = new FormData();
                    formData.append('action_upgrade', 'normal');
                    formData.append('is_ajax', '1');

                    fetch('dashboard.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'redirect') {
                            window.location.href = data.url;
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>