<?php
session_start();
include '../db.php'; 

if (isset($_SESSION['mitra_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = "SELECT * FROM mitra WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // CEK STATUS AKUN (Apakah masih Pending?)
        if ($row['status_akun'] == 'Pending') {
            $error = "Akun Anda belum aktif! Silakan selesaikan pembayaran langganan Anda.";
        } else {
            // Login sukses
            $_SESSION['mitra_id'] = $row['id'];
            $_SESSION['nama_mitra'] = $row['nama_mitra'];
            header("Location: dashboard.php");
            exit;
        }
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login Mitra - FasilBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 400px; width: 100%; border: none; border-radius: 12px; }
    </style>
</head>
<body>
    <div class="card login-card shadow p-4 bg-white">
        <div class="text-center mb-4">
            <h3 class="text-success fw-bold">FasilBook</h3>
            <p class="text-muted small">Panel Masuk Mitra Lapangan</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger text-center small py-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username Anda" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
            </div>
            <button type="submit" class="btn btn-success w-100 py-2 fw-bold">Masuk Ke Dashboard</button>
            <div class="text-center mt-3">
                <small class="text-muted">Belum menjadi mitra? <a href="register.php" class="text-success fw-bold text-decoration-none">Daftar sekarang</a></small>
            </div>
        </form>
    </div>
</body>
</html>