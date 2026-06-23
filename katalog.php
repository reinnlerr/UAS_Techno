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
        <h2 class="mb-4 text-center">Daftar Lapangan Futsal (Arcamanik & Sekitarnya)</h2>
        <div class="row g-4">
            <?php
            $result = $conn->query("SELECT * FROM lapangan");
            if($result && $result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $gambar = !empty($row['gambar']) ? $row['gambar'] : '';
                    echo '<div class="col-md-4">
                            <div class="card h-100 shadow-sm overflow-hidden border-0 rounded">';
                    if($gambar){
                        echo '<img src="'.$gambar.'" alt="'.$row['nama_lapangan'].'" class="card-img-top" style="height: 200px; object-fit: cover;">';
                    }
                    echo '      <div class="card-body">
                                    <h5 class="card-title text-primary">'.$row['nama_lapangan'].'</h5>
                                    <p class="card-text text-muted"><i class="fa fa-map-marker-alt me-1"></i>'.$row['lokasi'].'</p>
                                    <h6 class="fw-bold text-dark">Rp '.number_format($row['harga_per_jam'], 0, ',', '.').' / jam</h6>
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