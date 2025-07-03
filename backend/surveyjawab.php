<?php include 'header.php';
$ambil = $conn->query("SELECT * FROM survey WHERE idsurvey='$_GET[id]'");
$data = $ambil->fetch_assoc();
$idsiswa = $_SESSION['akun']['idsiswa'];
$ambiljawaban = $conn->query("SELECT * FROM jawaban left join siswa ON jawaban.idsiswa = siswa.idsiswa WHERE jawaban.idsiswa='$idsiswa' and idsurvey='$_GET[id]'");
$jawaban = $ambiljawaban->fetch_assoc();
if ($_SESSION["akun"]['level'] == 'Siswa') {
    if (!empty($jawaban)) {
        echo "<script>location='kuesionerhasil.php?id=$_GET[id]';</script>";
    }
}
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h2 class="page-title"> Jawab Survey </h2>
        </div>
        <div id="blog_page_area">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label>Nama Survey</label>
                                        <input type="text" class="form-control" name="namasurvey" value="<?= $data['namasurvey'] ?>" placeholder="Nama Survey" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea class="form-control" name="isi" id="isi" rows="10" readonly><?= $data['isi'] ?></textarea>
                                        <script>
                                            CKEDITOR.replace('isi');
                                        </script>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4>Jawab Survey</h4>
                                <br>
                                <form method="post">
                                    <?php $nomor = 1;
                                    $ambilrow = $conn->query("SELECT*FROM pertanyaan WHERE idsurvey='$_GET[id]' order by idpertanyaan asc");
                                    while ($data = $ambilrow->fetch_assoc()) { ?>
                                        <input type="hidden" name="idpertanyaan[]" value="<?php echo $data['idpertanyaan']; ?>">
                                        <input type="hidden" name="idsurvey" value="<?php echo $data['idsurvey']; ?>">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <tr>
                                                        <td width="10px"><?php echo $nomor ?>.</td>
                                                        <td><?php echo $data['pertanyaan']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>A. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" type="radio" value="A" required> <?php echo $data['a']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>B. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" type="radio" value="B"> <?php echo $data['b']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>C. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" type="radio" value="C"> <?php echo $data['c']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td>D. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" type="radio" value="D"> <?php echo $data['d']; ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                    <?php
                                        $nomor++;
                                    } ?>
                                    <div class="form-group">
                                        <button class="btn btn-primary float-right pull-right" type="submit" name="simpan" value="simpan" onclick="return confirm('Apakah anda yakin ingin mengsubmit jawaban ? Jawaban yang telah di submit tidak dapat dirubah kembali')">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if (isset($_POST['simpan'])) {
    $idsurvey = $_POST['idsurvey'];
    $pilihan    = $_POST['pilihan'];
    $idpertanyaan    = $_POST['idpertanyaan'];
    $ambilsurvey = $conn->query("SELECT * FROM pertanyaan
    WHERE idsurvey='$idsurvey'");
    $jumlah = $ambilsurvey->num_rows;
    $score    = 0;
    $benar    = 0;
    $salah    = 0;
    $kosong    = 0;

    for ($i = 0; $i < $jumlah; $i++) {
        $nomor    = $idpertanyaan[$i];
        if (empty($pilihan[$nomor])) {
            $kosong++;
        } else {
            $jawaban    = $pilihan[$nomor];
            $query    = mysqli_query($conn, "SELECT * FROM pertanyaan WHERE idpertanyaan='$nomor'");
            $cek    = mysqli_num_rows($query);
            if ($cek) {
                $benar++;
            } else {
                $salah++;
            }
        }
        $score    = 100 / $jumlah * $benar;
        $hasil    = number_format($score, 2);
    }
    $conn->query("INSERT INTO jawaban
		(idsiswa,idsurvey,benar,salah,nilai)
		VALUES('$idsiswa','$idsurvey','$benar','$salah','$hasil')") or die(mysqli_error($conn));
    $idjawaban = $conn->insert_id;
    $nomor = 0;
    for ($i = 0; $i < $jumlah; $i++) {
        $nomor    = $idpertanyaan[$i];
        if (empty($pilihan[$nomor])) {
            $kosong++;
        } else {
            $jawaban    = $pilihan[$nomor];
            $conn->query("INSERT INTO listjawaban
            (idjawaban,idpertanyaan,jawaban)
            VALUES('$idjawaban','$nomor','$jawaban')") or die(mysqli_error($conn));
        }
    }
    echo "<script>alert('Jawaban Anda Berhasil Di Simpan');</script>";
    echo "<script>location='kuesionerhasil.php?id=$idsurvey';</script>";
}
include 'footer.php';
?>