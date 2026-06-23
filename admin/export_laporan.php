<?php
session_start();
include '../db.php';

// 1. Keamanan: Hanya mitra yang login yang bisa akses
if (!isset($_SESSION['mitra_id'])) { exit("Akses ditolak."); }

$mitra_id = $_SESSION['mitra_id'];

// 2. Cek Paket: Starter TIDAK BOLEH download (sesuai strategi Freemium)
$q_cek = $conn->query("SELECT paket FROM mitra WHERE id = '$mitra_id'");
$data = $q_cek->fetch_assoc();
if ($data['paket'] == 'Starter') {
    die("Fitur ini hanya untuk paket Growth & Scale. Silakan Upgrade!");
}

// 3. Header agar browser mengenali ini sebagai file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Transaksi_FasilBook.xls");

// 4. Query data pesanan milik mitra
$sql = "SELECT p.*, l.nama_lapangan 
        FROM pesanan p 
        JOIN lapangan l ON p.lapangan_id = l.id 
        WHERE l.id_mitra = '$mitra_id'";
$result = $conn->query($sql);
?>

<table border="1">
    <thead>
        <tr>
            <th>ID Pesanan</th>
            <th>Nama Pemesan</th>
            <th>Lapangan</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['nama_pemesan']; ?></td>
            <td><?php echo $row['nama_lapangan']; ?></td>
            <td><?php echo $row['tanggal_booking']; ?></td>
            <td><?php echo $row['jam_mulai']; ?></td>
            <td><?php echo $row['status_pembayaran']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>