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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Layout ala Wartek Admin -->
            <div class="col-md-2 sidebar p-4">
                <h4 class="text-success mb-4"><i class="fa fa-futbol me-2"></i>FasilBook</h4>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link active" href="#"><i class="fa fa-chart-line me-2"></i>Monitor Utama</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.php"><i class="fa fa-sign-out-alt me-2"></i>Ke Web Utama</a></li>
                </ul>
            </div>
            
            <!-- Main Content Panel Berkelas -->
            <div class="col-md-10 p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Panel Manajemen Mitra FasilBook</h2>
                    <span class="badge bg-success p-2"><i class="fa fa-circle me-1"></i>Server Live (XAMPP)</span>
                </div>
                
                <!-- Statistik Ringkas -->
                <div class="row g-3 mb-5">
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

                <!-- Tabel Real-time Monitor Ketersediaan Jadwal -->
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-header bg-dark text-white p-3 fw-bold"><i class="fa fa-table me-2"></i>Log Pemesanan Masuk & Status Pembayaran QRIS</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle m-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Tim / Pemesan</th>
                                        <th>ID Lapangan</th>
                                        <th>Tanggal Booking</th>
                                        <th>Jam Mulai</th>
                                        <th>Status QRIS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = $conn->query("SELECT * FROM pesanan ORDER BY id DESC");
                                    if($result && $result->num_rows > 0){
                                        while($row = $result->fetch_assoc()){
                                            $badge_class = $row['status_pembayaran'] == 'Lunas' ? 'bg-success' : 'bg-warning text-dark';
                                            echo "<tr>
                                                    <td>#{$row['id']}</td>
                                                    <td class='fw-bold text-dark'>{$row['nama_pemesan']}</td>
                                                    <td>Lapangan #{$row['lapangan_id']}</td>
                                                    <td>{$row['tanggal_booking']}</td>
                                                    <td><i class='fa fa-clock text-muted me-1'></i>" . substr($row['jam_mulai'],0,5) . "</td>
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
                
            </div>
        </div>
    </div>
</body>
</html>