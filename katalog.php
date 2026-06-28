<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Katalog Lapangan - FasilBook</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-4 py-3">
        <a href="index.php" class="navbar-brand"><h1 class="text-primary m-0">FasilBook</h1></a>
        <a href="index.php" class="btn btn-outline-light">Kembali ke Beranda</a>
    </nav>
    <div class="container py-5">
        <h2 class="mb-4 text-center">Daftar Lapangan Futsal (Kota Bandung)</h2>
        <div class="row g-4">
            <?php
            $result = $conn->query("SELECT l.*, m.paket 
                                    FROM lapangan l 
                                    JOIN mitra m ON l.id_mitra = m.id 
                                    ORDER BY 
                                        CASE m.paket 
                                            WHEN 'Scale' THEN 1 
                                            WHEN 'Growth' THEN 2 
                                            WHEN 'Starter' THEN 3 
                                        END, l.id DESC");
            if($result && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $gambar = !empty($row['gambar']) ? $row['gambar'] : '';
                    $is_premium = ($row['paket'] == 'Scale');
                    $badge_html = '';
                    if ($is_premium) {
                        $badge_html = '<div class="position-absolute top-0 start-0 m-3 badge bg-warning text-dark fw-bold px-3 py-2 shadow-sm" style="z-index: 10; font-size: 0.75rem; border-radius: 50px; border: 1px solid rgba(255,255,255,0.4);"><i class="fa fa-star text-danger me-1"></i> PREMIUM PARTNER</div>';
                    }
                    echo '<div class="col-md-4">
                            <div class="card h-100 shadow-sm overflow-hidden border-0 rounded position-relative">
                                '.$badge_html.'';
                    if($gambar){
                        echo '<img src="'.$gambar.'" alt="'.$row['nama_lapangan'].'" class="card-img-top" style="height: 200px; object-fit: cover;">';
                    }
                    echo '      <div class="card-body">
                                    <h5 class="card-title text-primary">'.$row['nama_lapangan'].'</h5>
                                    <p class="card-text text-muted"><i class="fa fa-map-marker-alt me-1"></i>'.$row['lokasi'].'</p>
                                    '.($is_premium && !empty($row['harga_promo']) ? '
                                    <h6 class="mb-0"><span class="text-muted text-decoration-line-through small me-2" style="font-size:0.85rem;">Rp '.number_format($row['harga_per_jam'], 0, ',', '.').'</span> <span class="text-success fw-bold">Rp '.number_format($row['harga_promo'], 0, ',', '.').' / jam</span></h6>
                                    ' : '
                                    <h6 class="fw-bold text-dark">Rp '.number_format($row['harga_per_jam'], 0, ',', '.').' / jam</h6>
                                    ').'
                                </div>
                            </div>
                          </div>';
                }
            }
            ?>
        </div>
    </div>
</body>
</html>