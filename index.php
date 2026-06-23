<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>FasilBook - Pesan Lapangan Futsal</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 py-3 shadow">
        <a href="index.php" class="navbar-brand"><h2 class="text-primary m-0"><i class="fa fa-futbol me-2"></i>FasilBook</h2></a>
        <div class="collapse navbar-collapse">
            <div class="navbar-nav ms-auto pe-4">
                <a href="index.php" class="nav-item nav-link active text-white">Beranda</a>
                <a href="admin/dashboard.php" class="nav-item nav-link text-white-50">Dashboard Mitra</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-5 bg-dark hero-header mb-5 text-center text-white">
        <div class="container my-5 py-5">
            <h1 class="display-3 animated slideInLeft">Pesan Lapangan Futsal,<br>Kapan Saja, Di Mana Saja</h1>
            <p class="lead my-4">Solusi administrasi digital terintegrasi untuk komunitas futsal & pengelola venue.</p>
        </div>
    </div>

    <div class="container py-5">
        <h3 class="text-center mb-5 font-poppins">Katalog Lapangan Mitra (Arcamanik & Sekitarnya)</h3>
        <div class="row g-4">
            <?php
            $res = $conn->query("SELECT l.*, m.paket 
                                 FROM lapangan l 
                                 JOIN mitra m ON l.id_mitra = m.id 
                                 ORDER BY 
                                     CASE m.paket 
                                         WHEN 'Scale' THEN 1 
                                         WHEN 'Growth' THEN 2 
                                         WHEN 'Starter' THEN 3 
                                     END, l.id DESC");
            while($row = $res->fetch_assoc()){
                $gambar = !empty($row['gambar']) ? $row['gambar'] : '';
                $is_premium = ($row['paket'] == 'Scale');
                $badge_html = '';
                if ($is_premium) {
                    $badge_html = "<div class='position-absolute top-0 start-0 m-3 badge bg-warning text-dark fw-bold px-3 py-2 shadow-sm' style='z-index: 10; font-size: 0.75rem; border-radius: 50px; border: 1px solid rgba(255,255,255,0.4);'><i class='fa fa-star text-danger me-1'></i> PREMIUM PARTNER</div>";
                }
                echo "
                <div class='col-md-4'>
                    <div class='card h-100 shadow-sm border-0 rounded overflow-hidden position-relative'>
                        {$badge_html}
                        <div class='card-img-wrapper' style='height: 220px; overflow: hidden; position: relative;'>";
                if($gambar) {
                    echo "<img src='{$gambar}' alt='{$row['nama_lapangan']}' class='w-100 h-100' style='object-fit: cover;'>";
                } else {
                    echo "<div class='bg-secondary text-white p-5 text-center h-100 d-flex align-items-center justify-content-center' style='font-size: 24px;'><i class='fa fa-image fa-2x d-block mb-2'></i> Gambar Lapangan</div>";
                }
                echo "      </div>
                        <div class='card-body'>
                            <h5 class='card-title text-dark'>{$row['nama_lapangan']}</h5>
                            <p class='card-text text-muted'><i class='fa fa-map-marker-alt text-primary me-2'></i>{$row['lokasi']}</p>
                            <?php
                            $harga_display = '';
                            if ($is_premium && !empty($row['harga_promo'])) {
                                $harga_display = "<span class='text-muted text-decoration-line-through small me-2' style='font-size:0.85rem;'>Rp ".number_format($row['harga_per_jam'], 0, ',', '.')."</span> <span class='text-success fw-bold'>Rp ".number_format($row['harga_promo'], 0, ',', '.')." / jam</span>";
                            } else {
                                $harga_display = "<span class='text-primary fw-bold'>Rp ".number_format($row['harga_per_jam'], 0, ',', '.')." / jam</span>";
                            }
                            ?>
                            <h5 class='mb-3'><?php echo $harga_display; ?></h5>
                            <a href='booking.php?id={$row['id']}' class='btn btn-primary w-100 py-2'>Lihat Jadwal & Booking</a>
                        </div>
                    </div>
                </div>";
            }
            ?>
        </div>
    </div>
</body>
</html>