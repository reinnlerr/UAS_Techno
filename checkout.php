<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <title>Checkout Lapangan - FasilBook</title>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card p-4 shadow border-0 mx-auto" style="max-width: 500px;">
            <h3 class="text-center text-success mb-3">Rincian Checkout</h3>
            <table class="table table-borderless">
                <tr><td>Tanggal Main</td><td>: <strong><?php echo $_GET['tgl']; ?></strong></td></tr>
                <tr><td>Jam Mulai</td><td>: <strong><?php echo $_GET['jam']; ?></strong></td></tr>
            </table>
            <div class="border p-4 my-3 bg-white text-center rounded shadow-sm">
                <h5 class="mb-3 text-primary">Scan QRIS untuk Pembayaran</h5>
                <div class="bg-dark text-white p-5 mx-auto rounded" style="width: 180px; height: 180px;">[QRIS CODE PLACEHOLDER]</div>
                <p class="text-muted mt-2 small">Silakan selesaikan transaksi dengan aplikasi e-wallet Anda.</p>
            </div>
            <form action="proses_simulasi.php" method="POST">
                <input type="hidden" name="tgl" value="<?php echo $_GET['tgl']; ?>">
                <input type="hidden" name="jam" value="<?php echo $_GET['jam']; ?>">
                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                <div class="mb-3">
                    <input type="text" name="nama_pemesan" class="form-control" placeholder="Masukkan Nama Tim Anda" required>
                </div>
                <button class="btn btn-success w-100 py-3 fw-bold">Konfirmasi Pembayaran Lunas</button>
            </form>
        </div>
    </div>
</body>
</html>