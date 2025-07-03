<?php
file_exists('config/conn.php') ? include('config/conn.php') : include('../config/conn.php');
$idsurvey = "";
if (isset($_POST['submit'])) {
    $idsurvey = $_POST['idsurvey'];
} else {
    $idsurvey = $_GET['idsurvey'];
}
// echo $idsurvey;
$ambil_survey = $conn->query("SELECT * FROM survey WHERE idsurvey = '$idsurvey'");
$data_survey = $ambil_survey->fetch_assoc();

date_default_timezone_set('Asia/Hong_Kong');
// Get the current time as a Unix timestamp
$time = time();

// Convert the Unix timestamp to a human-readable format
$now = date('Y-m-d H:i:s', $time);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Hasil Laporan Survey</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table-centered {
            display: table;
            margin-left: auto;
            margin-right: auto;
            vertical-align: middle;
        }

        .table-centered thead th,
        .table-centered tbody td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">HASIL DATA</h1>
        <h2 class="text-center">EFISIONER</h2>
        <h3 class="text-center">KUESIONER "<?= $data_survey['namasurvey'] ?>"</h3>
        <p class="text-center"><?= $now ?></p>
        <hr>
        <table class="table table-bordered table-centered">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>SOAL</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>JUMLAH ORANG</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ambilpertanyaan = $conn->query("
            SELECT DISTINCT a.*, (SELECT COUNT(jawaban) FROM listjawaban WHERE idpertanyaan = a.idpertanyaan AND jawaban = 'a') AS pilihan_a,
            (SELECT COUNT(jawaban) FROM listjawaban WHERE idpertanyaan = a.idpertanyaan AND jawaban = 'b') AS pilihan_b,
            (SELECT COUNT(jawaban) FROM listjawaban WHERE idpertanyaan = a.idpertanyaan AND jawaban = 'c') AS pilihan_c,
            (SELECT COUNT(jawaban) FROM listjawaban WHERE idpertanyaan = a.idpertanyaan AND jawaban = 'd') AS pilihan_d,
            (SELECT COUNT(jawaban) FROM listjawaban WHERE idpertanyaan = a.idpertanyaan) AS jumlah_orang
            FROM pertanyaan a
            LEFT JOIN jawaban b ON a.idsurvey = b.idsurvey
            LEFT JOIN listjawaban c ON b.idjawaban = c.idjawaban
            WHERE a.idsurvey = '$idsurvey';
            ");

                $nomor = 1;
                ?>
                <?php while ($row = $ambilpertanyaan->fetch_assoc()) {
                ?>
                    <tr>
                        <td rowspan="2"><?= $nomor ?></td>
                        <td rowspan="2"><?= $row['pertanyaan'] ?></td>
                        <td><?= $row['a'] ?></td>
                        <td><?= $row['b'] ?></td>
                        <td><?= $row['c'] ?></td>
                        <td><?= $row['d'] ?></td>
                        <td rowspan="2"><?= $row['jumlah_orang'] ?></td>
                    </tr>
                    <tr>
                        <td><?= $row['pilihan_a'] ?></td>
                        <td><?= $row['pilihan_b'] ?></td>
                        <td><?= $row['pilihan_c'] ?></td>
                        <td><?= $row['pilihan_d'] ?></td>
                    </tr>
                <?php
                    $nomor++;
                } ?>
            </tbody>
        </table>
    </div>
</body>
<script>
    window.print();
</script>

</html>