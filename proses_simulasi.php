<?php
include 'db.php';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id = $_POST['id'];
    $tgl = $_POST['tgl'];
    $jam = $_POST['jam'];
    $nama = $_POST['nama_pemesan'];
    
    $ins = $conn->query("INSERT INTO pesanan (lapangan_id, nama_pemesan, tanggal_booking, jam_mulai, status_pembayaran) VALUES ('$id', '$nama', '$tgl', '$jam', 'Lunas')");
    if($ins){
        $pesanan_id = $conn->insert_id;
        header("Location: bon.php?pesanan_id=$pesanan_id");
        exit;
    } else {
        echo "Gagal memproses data.";
    }
}
?>