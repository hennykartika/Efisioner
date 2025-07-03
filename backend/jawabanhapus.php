<?php
file_exists('config/conn.php') ? include('config/conn.php') : include('../config/conn.php');
$conn->query("DELETE FROM jawaban WHERE idjawaban='$_GET[id]'");
$conn->query("DELETE FROM listjawaban WHERE idjawaban='$_GET[id]'");
echo "<script>alert('Data Berhasil Di Hapus');</script>";
echo "<script>location='kuesionerpenjawab.php?id=$_GET[idsurvey]';</script>";
