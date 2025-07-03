<?php
file_exists('config/conn.php') ? include('config/conn.php') : include('../config/conn.php');
$conn->query("DELETE FROM pertanyaan WHERE idpertanyaan='$_GET[id]'");
echo "<script>alert('Data Berhasil Di Hapus');</script>";
echo "<script>location='kuesioner-edit.php?id=$_GET[idsurvey]';</script>";
