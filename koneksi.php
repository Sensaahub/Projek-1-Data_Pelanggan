<?php
/* ganti host, user, pass bila perlu */
$koneksi = mysqli_connect("localhost", "root", "", "db_pelanggan");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
