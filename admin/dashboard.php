<?php include '../db.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Admin - FasilBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-4 py-3">
        <a href="../index.php" class="navbar-brand"><h1 class="text-primary m-0">Admin FasilBook</h1></a>
        <a href="../index.php" class="btn btn-outline-light">Kembali ke Beranda</a>
    </nav>
    <div class="container py-5">
        <h2>Data Reservasi Masuk</h2>
        <table class="table table-striped table-bordered mt-4">
            <thead class="table-dark">
                <tr><th>ID</th><th>Tim</th><th>Tanggal</th><th>Jam</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM pesanan ORDER BY id DESC");
                if($result && $result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        echo "<tr><td>{$row['id']}</td><td>{$row['nama_pemesan']}</td><td>{$row['tanggal']}</td><td>{$row['jam']}</td><td>{$row['status']}</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>Belum ada reservasi</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>