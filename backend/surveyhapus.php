<?php
file_exists('config/conn.php') ? include('config/conn.php') : include('../config/conn.php');
$conn->query("DELETE FROM survey WHERE idsurvey='$_GET[id]'");
echo "<script>alert('Data Berhasil Di Hapus');</script>";
echo "<script>location='surveydaftar.php';</script>";
