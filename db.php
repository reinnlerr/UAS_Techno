<?php
$conn = new mysqli("localhost", "root", "", "fasilbook");
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }
?>