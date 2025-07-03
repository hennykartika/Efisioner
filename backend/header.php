<?php
file_exists('config/conn.php') ? include('config/conn.php') : include('../config/conn.php');
session_start();
if (!isset($_SESSION['akun'])) {
  echo "<script>location='../login.php';</script>";
  exit();
}
function tanggal($tgl)
{
  $tanggal = substr($tgl, 8, 2);
  $bulan = getBulan(substr($tgl, 5, 2));
  $tahun = substr($tgl, 0, 4);
  return $tanggal . ' ' . $bulan . ' ' . $tahun;
}
function getBulan($bln)
{
  switch ($bln) {
    case 1:
      return "Januari";
      break;
    case 2:
      return "Februari";
      break;
    case 3:
      return "Maret";
      break;
    case 4:
      return "April";
      break;
    case 5:
      return "Mei";
      break;
    case 6:
      return "Juni";
      break;
    case 7:
      return "Juli";
      break;
    case 8:
      return "Agustus";
      break;
    case 9:
      return "September";
      break;
    case 10:
      return "Oktober";
      break;
    case 11:
      return "November";
      break;
    case 12:
      return "Desember";
      break;
  }
}
function hari($hari)
{
  $hari = date("D", strtotime($hari));
  switch ($hari) {
    case 'Sun':
      $hari_ini = "Minggu";
      break;

    case 'Mon':
      $hari_ini = "Senin";
      break;

    case 'Tue':
      $hari_ini = "Selasa";
      break;

    case 'Wed':
      $hari_ini = "Rabu";
      break;

    case 'Thu':
      $hari_ini = "Kamis";
      break;

    case 'Fri':
      $hari_ini = "Jumat";
      break;

    case 'Sat':
      $hari_ini = "Sabtu";
      break;

    default:
      $hari_ini = "Tidak di ketahui";
      break;
  }

  return $hari_ini;
}

function wordlimiter($string, $limit)
{
  $word = explode(" ", $string);
  return implode(" ", array_splice($word, 0, $limit));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Efisioner</title>
  <link rel="stylesheet" href="../assets/backend/node_modules/font-awesome/css/font-awesome.min.css" />
  <link rel="stylesheet" href="../assets/backend/node_modules/perfect-scrollbar/dist/css/perfect-scrollbar.min.css" />
  <link rel="stylesheet" href="../assets/backend/css/style.css" />
  <link rel="shortcut icon" href="../foto/logo.png" />
  <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <script src="../assets/ckeditor/ckeditor.js"></script>
</head>
<?php
if ($_SESSION["akun"]['level'] == 'Admin') {
  $warna = "bg-danger";
} else {
  $warna = "";
}
?>

<body>
  <div class="container-scroller">
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row" style="background-color: #0077B6;">
      <div class="bg-white text-center navbar-brand-wrapper">
        <a class="navbar-brand brand-logo" href="<?= base_url() ?>">Efisioner</a>
        <a class="navbar-brand brand-logo-mini" href="index.html"><img src="../foto/logo.png" style="width: 50px;"></a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center">
        <button class="navbar-toggler navbar-toggler d-none d-lg-block navbar-dark align-self-center mr-3" type="button" data-toggle="minimize">
          <span class="navbar-toggler-icon"></span>
        </button>
        <button class="navbar-toggler navbar-dark navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </nav>
    <div class="container-fluid">
      <div class="row row-offcanvas row-offcanvas-right">
        <nav class="sidebar sidebar-offcanvas" id="sidebar" style="background-color: #0077B6;">
          <div class="user-info mt-5">
            <img src="../foto/logo.png" style="width: 50px;">
            <p class="name"><?= $_SESSION["akun"]['nama'] ?></p>
            <p class="designation"><?= $_SESSION["akun"]['level'] ?></p>
          </div>
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="index.php">
                <span class="menu-title text-white">Kuesioner User</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="surveydaftar.php">
                <span class="icon-bg"><i class="mdi mdi-human"></i></span>
                <span class="menu-title text-white">Jawaban Responden</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="surveytambah.php">
                <span class="menu-title text-white">Tambah Kuesioner</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="profil.php">
                <span class="icon-bg"><i class="mdi mdi-book"></i></span>
                <span class="menu-title text-white">Profil</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php" onclick="return confirm('Apakah Anda Yakin Mau Keluar?')">
                <span class="icon-bg"><i class="mdi mdi-logout"></i></span>
                <span class="menu-title text-white">Logout</span>
              </a>
            </li>
          </ul>
        </nav>