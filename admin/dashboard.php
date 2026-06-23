<?php include '../db.php'; ?>
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
        .card-counter { background-color: white; border-left: 5px solid #2FB95D; }

        /* Kartu lapangan di panel mitra */
        .lapangan-card { transition: transform 0.2s, box-shadow 0.2s; }
        .lapangan-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.13) !important; }
        .lapangan-card .badge-id {
            position: absolute; top: 10px; left: 10px;
            background: rgba(0,0,0,0.65); color: #fff;
            font-size: 0.78rem; font-weight: 700;
            padding: 3px 9px; border-radius: 20px;
            letter-spacing: 0.5px;
        }
        .lapangan-card img { object-fit: cover; height: 160px; width: 100%; }

        /* Thumbnail lapangan di tabel */
        .tbl-thumb { width: 44px; height: 34px; object-fit: cover; border-radius: 6px; border: 1px solid #dee2e6; }
        .lapangan-cell { display: flex; align-items: center; gap: 9px; }
        .lapangan-cell .lap-name { font-weight: 600; color: #1a1a2e; font-size: 0.87rem; }
        .lapangan-cell .lap-id { font-size: 0.72rem; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-4">
                <h4 class="text-success mb-4"><i class="fa fa-futbol me-2"></i>FasilBook</h4>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="#"><i class="fa fa-chart-line me-2"></i>Monitor Utama</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.php"><i class="fa fa-sign-out-alt me-2"></i>Ke Web Utama</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Panel Manajemen Mitra FasilBook</h2>
                    <span class="badge bg-success p-2"><i class="fa fa-circle me-1"></i>Server Live (XAMPP)</span>
                </div>

                <!-- Statistik Ringkas -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card p-3 shadow-sm card-counter">
                            <small class="text-muted">Total Reservasi Terdaftar</small>
                            <?php $c1 = $conn->query("SELECT id FROM pesanan"); ?>
                            <h3><?php echo $c1->num_rows; ?> Transaksi</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-3 shadow-sm card-counter" style="border-left-color: #ffc107;">
                            <small class="text-muted">Lapangan Aktif</small>
                            <?php $c2 = $conn->query("SELECT id FROM lapangan"); ?>
                            <h3><?php echo $c2->num_rows; ?> Venue</h3>
                        </div>
                    </div>
                </div>

                <!-- ===== SECTION: Daftar Lapangan (Visual Reference) ===== -->
                <div class="mb-5">
                    <h5 class="fw-bold mb-3"><i class="fa fa-th-large me-2 text-success"></i>Daftar Lapangan Terdaftar</h5>
                    <div class="row g-3">
                        <?php
                        $ql = $conn->query("SELECT * FROM lapangan ORDER BY id ASC");
                        if ($ql && $ql->num_rows > 0) {
                            while ($lap = $ql->fetch_assoc()) {
                                $img_src = '../' . htmlspecialchars($lap['gambar']);
                                $no_img = 'https://via.placeholder.com/400x160/2FB95D/FFFFFF?text=Lapangan+' . $lap['id'];
                                ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="card border-0 shadow-sm rounded overflow-hidden lapangan-card position-relative">
                                        <span class="badge-id">ID #<?php echo $lap['id']; ?></span>
                                        <img src="<?php echo $img_src; ?>"
                                             alt="<?php echo htmlspecialchars($lap['nama_lapangan']); ?>"
                                             onerror="this.src='<?php echo $no_img; ?>'"
                                        >
                                        <div class="card-body py-2 px-3">
                                            <div class="fw-bold text-dark" style="font-size:0.9rem;"><?php echo htmlspecialchars($lap['nama_lapangan']); ?></div>
                                            <div class="text-muted" style="font-size:0.78rem;"><i class="fa fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($lap['lokasi']); ?></div>
                                            <div class="text-success fw-bold mt-1" style="font-size:0.85rem;">
                                                Rp <?php echo number_format($lap['harga_per_jam'],0,',','.'); ?> / jam
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- ===== SECTION: Log Pemesanan ===== -->
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-header bg-dark text-white p-3 fw-bold">
                        <i class="fa fa-table me-2"></i>Log Pemesanan Masuk &amp; Status Pembayaran QRIS
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle m-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Tim / Pemesan</th>
                                        <th>Lapangan</th>
                                        <th>Tanggal Booking</th>
                                        <th>Jam Mulai</th>
                                        <th>Status QRIS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = $conn->query("
                                        SELECT p.*, l.nama_lapangan, l.gambar
                                        FROM pesanan p
                                        LEFT JOIN lapangan l ON p.lapangan_id = l.id
                                        ORDER BY p.id DESC
                                    ");
                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $badge_class = $row['status_pembayaran'] == 'Lunas' ? 'bg-success' : 'bg-warning text-dark';
                                            $img_src    = '../' . htmlspecialchars($row['gambar'] ?? '');
                                            $no_img     = 'https://via.placeholder.com/44x34/2FB95D/FFFFFF?text=' . $row['lapangan_id'];
                                            $nama_lap   = htmlspecialchars($row['nama_lapangan'] ?? 'Lapangan #' . $row['lapangan_id']);
                                            echo "
                                            <tr>
                                                <td class='text-muted'>#{$row['id']}</td>
                                                <td class='fw-bold text-dark'>{$row['nama_pemesan']}</td>
                                                <td>
                                                    <div class='lapangan-cell'>
                                                        <img src='{$img_src}' class='tbl-thumb'
                                                             onerror=\"this.src='{$no_img}'\"
                                                             alt='{$nama_lap}'>
                                                        <div>
                                                            <div class='lap-name'>{$nama_lap}</div>
                                                            <div class='lap-id'>ID #{$row['lapangan_id']}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{$row['tanggal_booking']}</td>
                                                <td><i class='fa fa-clock text-muted me-1'></i>" . substr($row['jam_mulai'], 0, 5) . "</td>
                                                <td><span class='badge {$badge_class}'>{$row['status_pembayaran']}</span></td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Belum ada data reservasi masuk.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!-- /col-md-10 -->
        </div><!-- /row -->
    </div><!-- /container-fluid -->
</body>
</html>