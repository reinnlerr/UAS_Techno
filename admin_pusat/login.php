<?php
session_start();
include '../db.php'; 

if (isset($_SESSION['superadmin'])) {
    header("Location: dashboard.php"); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek ke tabel admin pusat
    $query = $conn->query("SELECT * FROM admin WHERE username = '$username'");
    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        // Karena di SQL Anda menggunakan password_hash()
        if (password_verify($password, $row['password'])) {
            $_SESSION['superadmin'] = true;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password Salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login Super Admin - FasilBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card p-4 shadow" style="width: 350px;">
        <h4 class="text-center text-primary mb-3">Login Pusat FasilBook</h4>
        <?php if($error) echo "<div class='alert alert-danger py-2 small'>$error</div>"; ?>
        <form action="" method="POST">
            <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button class="btn btn-primary w-100 fw-bold">Masuk Sistem</button>
        </form>
    </div>
</body>
</html>