<?php
session_start();
include '../db.php';

// Pengecekan Login
if (!isset($_SESSION['mitra_id'])) {
    header("Location: login.php");
    exit;
}

$mitra_id = $_SESSION['mitra_id'];

// 1. CEK BATAS MAKSIMAL LAPANGAN (LIMIT)
$q_mitra = $conn->query("SELECT paket FROM mitra WHERE id = '$mitra_id'");
$paket = $q_mitra->fetch_assoc()['paket'];

$q_count = $conn->query("SELECT COUNT(id) as total FROM lapangan WHERE id_mitra = '$mitra_id'");
$total_lapangan = $q_count->fetch_assoc()['total'];

// Tentukan Batas Lapangan berdasarkan Paket
if ($paket == 'Scale') {
    $batas_lapangan = 5;
} elseif ($paket == 'Growth') {
    $batas_lapangan = 2;
} else {
    $batas_lapangan = 1;
}

// Tendang user kembali ke dashboard jika sudah melebihi batas
if ($total_lapangan >= $batas_lapangan) {
    echo "<script>alert('Batas maksimal lapangan untuk paket $paket Anda sudah tercapai!'); window.location.href='dashboard.php';</script>";
    exit;
}

// 2. PROSES FORM KETIKA DISUBMIT
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lapangan = mysqli_real_escape_string($conn, $_POST['nama_lapangan']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga_per_jam']);
    
    // Proses Upload Gambar
    $gambar_path = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $nama_file_baru = 'lap_' . $mitra_id . '_' . time() . '.' . $ext;
        $folder_tujuan = '../img/' . $nama_file_baru;
        
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $folder_tujuan)) {
            // Path yang akan disimpan ke database (disesuaikan agar bisa dipanggil dari index.php luar)
            $gambar_path = 'img/' . $nama_file_baru;
        } else {
            $error = "Gagal mengunggah gambar.";
        }
    } else {
        $error = "Gambar lapangan wajib diunggah.";
    }

    // Jika tidak ada error gambar, simpan ke database
    if (empty($error)) {
        $query = "INSERT INTO lapangan (id_mitra, nama_lapangan, lokasi, harga_per_jam, gambar) 
                  VALUES ('$mitra_id', '$nama_lapangan', '$lokasi', '$harga', '$gambar_path')";
        
        if ($conn->query($query)) {
            echo "<script>alert('Lapangan berhasil ditambahkan!'); window.location.href='dashboard.php';</script>";
            exit;
        } else {
            $error = "Terjadi kesalahan database: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tambah Lapangan - FasilBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>body { background-color: #f4f7f6; }</style>
</head>
<body>
    <div class="container py-5">
        <div class="card shadow border-0 mx-auto" style="max-width: 600px;">
            <div class="card-header bg-success text-white p-3">
                <h5 class="mb-0 fw-bold"><i class="fa fa-plus-circle me-2"></i>Tambah Lapangan Baru</h5>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="alert alert-info py-2 small">
                    <i class="fa fa-info-circle me-1"></i> Anda menggunakan Paket <strong><?php echo $paket; ?></strong>. Kapasitas tersisa: <strong><?php echo ($batas_lapangan - $total_lapangan); ?> Lapangan</strong>.
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Lapangan / Court</label>
                        <input type="text" name="nama_lapangan" class="form-control" placeholder="Contoh: Lapangan A Sintetis" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Lokasi / Alamat Lengkap</label>
                        <textarea name="lokasi" class="form-control" rows="2" placeholder="Masukkan alamat lengkap lapangan" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Harga Sewa per Jam (Rp)</label>
                        <input type="number" name="harga_per_jam" class="form-control" placeholder="Contoh: 75000" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">Foto Lapangan</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*" required>
                        <small class="text-muted">Format yang diizinkan: JPG, PNG, JPEG.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="dashboard.php" class="btn btn-secondary w-50 fw-bold">Batal</a>
                        <button type="submit" class="btn btn-success w-50 fw-bold">Simpan Lapangan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>