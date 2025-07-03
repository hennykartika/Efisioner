<?php include 'head.php' ?>
<?php
$idsurvey = $_SESSION['formulir']['idsurvey'];
$ambil = $conn->query("SELECT * FROM survey WHERE idsurvey='$idsurvey'");
$data = $ambil->fetch_assoc();
?>
<div class="container">
    <div class="row gx-5">
        <div class="col-lg-12">
            <div class="contact-form-area mb-70">
                <h1 class="text-center mb-4">Isi Jawaban Survey <?= $data['namasurvey'] ?></h1>
                <form action="" method="post">
                    <div class="row g-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td width="150px">Nama</td>
                                        <td><?= $_SESSION['formulir']['nama']; ?>
                                    </tr>
                                    <tr>
                                        <td width="150px">Jenis Kelamin</td>
                                        <td><?= $_SESSION['formulir']['jekel']; ?>
                                    </tr>
                                    <tr>
                                        <td width="150px">Usia</td>
                                        <td><?= $_SESSION['formulir']['usia']; ?>
                                    </tr>
                                    <tr>
                                        <td width="150px">Pendidikan</td>
                                        <td><?= $_SESSION['formulir']['pendidikan']; ?>
                                    </tr>
                                    <tr>
                                        <td width="150px">Pekerjaan</td>
                                        <td><?= $_SESSION['formulir']['pekerjaan']; ?>
                                    </tr>
                                    <tr>
                                        <td width="150px">Jenis Layanan</td>
                                        <td><?= $_SESSION['formulir']['jenislayanan']; ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php $nomor = 1;
                        $ambilrow = $conn->query("SELECT*FROM pertanyaan WHERE idsurvey='$idsurvey' order by idpertanyaan asc");
                        while ($data = $ambilrow->fetch_assoc()) { ?>
                            <input type="hidden" name="idpertanyaan[]" value="<?php echo $data['idpertanyaan']; ?>">
                            <input type="hidden" name="idsurvey" value="<?php echo $idsurvey; ?>">
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
                        <button class="btn btn-dark py-3" name="simpan" value="simpan" type="submit">Kirim Jawaban</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<br><br> <br><br>
<?php include 'foot.php' ?>
<script>
    const lainnyaRadio = document.getElementById('lainnya');
    const pekerjaanInput = document.getElementById('pekerjaaninput');

    lainnyaRadio.addEventListener('click', function() {
        pekerjaanInput.style.display = 'block';
    });

    const otherRadios = document.querySelectorAll('input[name="pekerjaan"]:not(#lainnya)');

    for (let radio of otherRadios) {
        radio.addEventListener('click', function() {
            pekerjaanInput.style.display = 'none';
        });
    }
</script>
<?php
if (isset($_POST['simpan'])) {
    $nama = $_SESSION['formulir']['nama'];
    $jekel = $_SESSION['formulir']['jekel'];
    $usia = $_SESSION['formulir']['usia'];
    $pendidikan = $_SESSION['formulir']['pendidikan'];
    $pekerjaan = $_SESSION['formulir']['pekerjaan'];
    $jenislayanan = $_SESSION['formulir']['jenislayanan'];
    $idsurvey = $_POST['idsurvey'];
    $pilihan    = $_POST['pilihan'];
    $idpertanyaan    = $_POST['idpertanyaan'];
    $ambilsurvey = $conn->query("SELECT * FROM pertanyaan
    WHERE idsurvey='$idsurvey'");
    $jumlah = $ambilsurvey->num_rows;
    $conn->query("INSERT INTO jawaban
		(nama,jekel,usia,pendidikan,pekerjaan,jenislayanan,idsurvey)
		VALUES('$nama','$jekel','$usia','$pendidikan','$pekerjaan','$jenislayanan','$idsurvey')") or die(mysqli_error($conn));
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
    echo "<script>alert('Jawaban survey anda berhasil di simpan. Terima kasih telah berpastisipasi');</script>";
    echo "<script>location='index.php';</script>";
}
?>

</html