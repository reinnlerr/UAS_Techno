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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 py-3">
        <a href="index.php" class="navbar-brand">
            <h1 class="text-primary m-0"><i class="fa fa-futbol me-3"></i>FasilBook</h1>
        </a>
        <div class="collapse navbar-collapse">
            <div class="navbar-nav ms-auto py-0 pe-4">
                <a href="index.php" class="nav-item nav-link active">Beranda</a>
                <a href="katalog.php" class="nav-item nav-link">Katalog</a>
                <a href="admin/dashboard.php" class="nav-item nav-link">Login Mitra</a>
            </div>
            <a href="#booking" class="btn btn-primary py-2 px-4">Booking Cepat</a>
        </div>
    </nav>

    <div class="container-fluid py-5 bg-dark text-white mb-5">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white">Pesan Lapangan Futsal,<br>Kapan Saja, Di Mana Saja</h1>
            <p class="lead mb-4">Platform digital terintegrasi untuk memudahkan proses reservasi lapangan futsal untuk komunitas dan individu.</p>
            <a href="katalog.php" class="btn btn-primary py-3 px-5">Lihat Katalog Lapangan</a>
        </div>
    </div>

    <div class="container py-5" id="booking">
        <div class="row justify-content-center">
            <div class="col-lg-8 bg-light p-5 rounded shadow">
                <h2 class="text-center mb-4">Cek & Booking Jadwal</h2>
                <form action="kirim_pesan.php" method="POST">
                    <div class="mb-3">
                        <label>Nama Pemesan / Tim</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Pilih Lapangan</label>
                        <select name="lapangan_id" class="form-select" required>
                            <option value="1">Futsal Merdeka Bandung</option>
                            <option value="2">Supratman Futsal</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3">Konfirmasi Booking</button>
                </form>
            </div>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>
</html>