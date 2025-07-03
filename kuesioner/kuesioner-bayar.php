<?php
file_exists('config/conn.php') ? include('config/conn.php') : include('../config/conn.php');
$conn->query("UPDATE survey
SET bayar = 1, updated_at=NOW()
WHERE idsurvey = '$_GET[id]';");
echo "<script>alert('Pembayaran Berhasil');</script>";
echo "<script>location='kuesioner-anda.php';</script>";
