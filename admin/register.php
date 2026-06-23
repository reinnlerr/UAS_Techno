<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_mitra']);
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $paket = mysqli_real_escape_string($conn, $_POST['paket']);

    // Tentukan Komisi dan Status Akun berdasarkan Paket
    $komisi = 8;
    $status_akun = 'Aktif'; // Starter langsung aktif

    if ($paket == 'Growth') {
        $komisi = 6;
        $status_akun = 'Pending'; // Harus bayar dulu
    } elseif ($paket == 'Scale') {
        $komisi = 4;
        $status_akun = 'Pending'; // Harus bayar dulu
    }

    // Cek apakah username sudah dipakai
    $cek = $conn->query("SELECT id FROM mitra WHERE username = '$user'");
    if ($cek->num_rows > 0) {
        $error = "Username sudah terdaftar, silakan gunakan yang lain!";
    } else {
        // Simpan data mitra baru
        $query = "INSERT INTO mitra (nama_mitra, username, password, paket, komisi_persen, status_akun) 
                  VALUES ('$nama', '$user', '$pass', '$paket', '$komisi', '$status_akun')";
        
        if ($conn->query($query)) {
            $id_mitra = $conn->insert_id;
            
            if ($paket == 'Starter') {
                // Jika gratis, langsung arahkan ke login
                echo "<script>alert('Pendaftaran Berhasil! Akun Starter Anda sudah aktif.'); window.location.href='login.php';</script>";
            } else {
                // Jika berbayar, arahkan ke halaman pembayaran langganan
                header("Location: pembayaran_mitra.php?id=" . $id_mitra);
                exit;
            }
        } else {
            $error = "Gagal mendaftar: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar Mitra - FasilBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px 0; }
        .register-card { max-width: 500px; width: 100%; border: none; border-radius: 12px; }
        .paket-box { border: 2px solid #dee2e6; border-radius: 8px; padding: 15px; cursor: pointer; transition: 0.3s; }
        .paket-box:hover { border-color: #2FB95D; background-color: #f8fff9; }
        .paket-radio:checked + .paket-box { border-color: #2FB95D; background-color: #e8f9ed; box-shadow: 0 4px 10px rgba(47,185,93,0.2); }
    </style>
</head>
<body>
    <div class="card register-card shadow p-4 bg-white">
        <div class="text-center mb-4">
            <h3 class="text-success fw-bold">Bergabung Bersama FasilBook</h3>
            <p class="text-muted small">Daftarkan lapangan Anda dan jangkau lebih banyak pemain.</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center small py-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Nama Pengelola / Lapangan</label>
                <input type="text" name="nama_mitra" class="form-control" placeholder="Contoh: Futsal Jaya Abadi" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-bold">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Buat username" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Buat password" required>
                </div>
            </div>

            <label class="form-label text-muted small fw-bold mt-2">Pilih Paket Langganan</label>
            <div class="mb-2">
                <input class="d-none paket-radio" type="radio" name="paket" id="p1" value="Starter" checked>
                <label class="paket-box d-block w-100 m-0" for="p1">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="fw-bold mb-0">Starter</h6><small class="text-muted">Gratis, Komisi 8%</small></div>
                        <h5 class="text-success mb-0 fw-bold">Rp 0</h5>
                    </div>
                </label>
            </div>
            <div class="mb-2">
                <input class="d-none paket-radio" type="radio" name="paket" id="p2" value="Growth">
                <label class="paket-box d-block w-100 m-0" for="p2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="fw-bold mb-0 text-info">Growth</h6><small class="text-muted">Laporan Lengkap, Komisi 6%</small></div>
                        <h5 class="text-success mb-0 fw-bold">Rp 99K<small class="fs-6 text-muted">/bln</small></h5>
                    </div>
                </label>
            </div>
            <div class="mb-4">
                <input class="d-none paket-radio" type="radio" name="paket" id="p3" value="Scale">
                <label class="paket-box d-block w-100 m-0" for="p3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><h6 class="fw-bold mb-0 text-primary">Scale</h6><small class="text-muted">Multi-Lapangan, Komisi 4%</small></div>
                        <h5 class="text-success mb-0 fw-bold">Rp 249K<small class="fs-6 text-muted">/bln</small></h5>
                    </div>
                </label>
            </div>

            <button type="submit" class="btn btn-success w-100 py-2 fw-bold">Daftar Sekarang</button>
            <div class="text-center mt-3">
                <small class="text-muted">Sudah punya akun? <a href="login.php" class="text-success fw-bold text-decoration-none">Login di sini</a></small>
            </div>
        </form>
    </div>
</body>
</html>