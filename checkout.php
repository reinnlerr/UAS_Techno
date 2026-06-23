<?php
include 'db.php';

$id_lapangan = isset($_GET['id']) ? $_GET['id'] : 0;

// Ambil data lapangan sekaligus data paket & komisi dari mitra pemilik lapangan tersebut
$query = $conn->query("
    SELECT l.nama_lapangan, l.harga_per_jam, m.paket, m.komisi_persen 
    FROM lapangan l 
    JOIN mitra m ON l.id_mitra = m.id 
    WHERE l.id = '$id_lapangan'
");
$data = $query->fetch_assoc();

if(!$data) die("Data lapangan tidak ditemukan.");

$harga_lapangan = $data['harga_per_jam'];
$persen_komisi  = $data['komisi_persen'];

// HITUNG BIAYA LAYANAN/ADMIN DINAMIS (Berdasarkan paket mitra)
$biaya_admin = ($harga_lapangan * $persen_komisi) / 100;
$total_bayar = $harga_lapangan + $biaya_admin;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Checkout Lapangan - FasilBook</title>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card p-4 shadow border-0 mx-auto" style="max-width: 500px;">
            <h3 class="text-center text-success mb-3">Rincian Pembayaran</h3>
            
            <div class="bg-white border rounded p-3 mb-3">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="text-muted">Lapangan</td><td class="text-end fw-bold"><?php echo htmlspecialchars($data['nama_lapangan']); ?></td></tr>
                    <tr><td class="text-muted">Tanggal Main</td><td class="text-end fw-bold"><?php echo htmlspecialchars($_GET['tgl']); ?></td></tr>
                    <tr><td class="text-muted">Jam Mulai</td><td class="text-end fw-bold"><?php echo htmlspecialchars($_GET['jam']); ?></td></tr>
                    <tr><td colspan="2"><hr class="my-2"></td></tr>
                    <tr><td class="text-muted">Harga Sewa</td><td class="text-end">Rp <?php echo number_format($harga_lapangan, 0, ',', '.'); ?></td></tr>
                    
                    <tr>
                        <td class="text-muted">Biaya Layanan (<?php echo $persen_komisi; ?>%)</td>
                        <td class="text-end text-danger">Rp <?php echo number_format($biaya_admin, 0, ',', '.'); ?></td>
                    </tr>
                    
                    <tr><td colspan="2"><hr class="my-2"></td></tr>
                    <tr><td><h5 class="mb-0">Total Pembayaran</h5></td><td class="text-end"><h5 class="text-success fw-bold mb-0">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></h5></td></tr>
                </table>
            </div>

            <div class="border p-4 my-3 bg-white text-center rounded shadow-sm">
                <h6 class="mb-3 text-primary fw-bold">Scan QRIS FasilBook</h6>
                <img src="img/qris_dummy.png" alt="QRIS" class="img-fluid border p-2 rounded mb-2" style="max-width: 200px;">
                <p class="text-muted mt-2 small">Sistem Satu Pintu FasilBook menjamin keamanan dana.</p>
            </div>

            <form action="proses_simulasi.php" method="POST">
                <input type="hidden" name="tgl" value="<?php echo htmlspecialchars($_GET['tgl']); ?>">
                <input type="hidden" name="jam" value="<?php echo htmlspecialchars($_GET['jam']); ?>">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_lapangan); ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Nama Tim / Pemesan</label>
                    <input type="text" name="nama_pemesan" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100 py-3 fw-bold">Kirim Pesanan</button>
            </form>
        </div>
    </div>
</body>
</html>