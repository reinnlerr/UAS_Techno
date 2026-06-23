<?php include 'db.php'; $id = $_GET['id']; $tgl = $_GET['tgl'] ?? date('Y-m-d'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <title>Pilih Jadwal - FasilBook</title>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card p-4 shadow-sm border-0">
            <h3 class="font-poppins mb-3"><i class="fa fa-calendar-alt text-primary me-2"></i>Pilih Tanggal & Jam Bermain</h3>
            <form method="GET" class="mb-4">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <label class="form-label fw-bold">Pilih Tanggal:</label>
                <input type="date" name="tgl" value="<?php echo $tgl; ?>" onchange="this.form.submit()" class="form-control form-control-lg">
            </form>
            
            <h5 class="mb-3 text-muted">Klik Jam Kosong Hari Ini:</h5>
            <div class="row g-2">
            <?php
            for($i=8; $i<22; $i++){
                $jam = sprintf("%02d:00:00", $i);
                $cek = $conn->query("SELECT id FROM pesanan WHERE lapangan_id=$id AND tanggal_booking='$tgl' AND jam_mulai='$jam'");
                if($cek->num_rows > 0) {
                    echo "<div class='col-md-3 col-6'><button class='btn btn-danger w-100 py-3 fw-bold shadow-sm' disabled><i class='fa fa-ban me-1'></i> $i:00 (Terisi)</button></div>";
                } else {
                    echo "<div class='col-md-3 col-6'><a href='checkout.php?id=$id&tgl=$tgl&jam=$jam' class='btn btn-outline-success w-100 py-3 fw-bold shadow-sm'>$i:00 (Tersedia)</a></div>";
                }
            }
            ?>
            </div>
        </div>
    </div>
</body>
</html>