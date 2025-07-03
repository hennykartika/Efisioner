<?php
include('../layout/header.php');
if (!isset($_SESSION['akun'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!')</script>";
    echo "<script>location='../login.php';</script>";
    exit();
}
$id_user = $_SESSION['akun']['id'];
$ambil = $conn->query("SELECT * FROM akun WHERE id='$id_user'");
$data_akun = $ambil->fetch_assoc();
$tanggalLahir = $data_akun['tanggal_lahir'];
$today = new DateTime();
$tanggalLahirObj = new DateTime($tanggalLahir);
$diff = $today->diff($tanggalLahirObj);
$usia = $diff->y;

$ambil_survey = $conn->query("SELECT * FROM survey WHERE idsurvey='$_GET[id]'");
$data = $ambil_survey->fetch_assoc();
$data_survey = $data;
?>
<hr>
<div class="container">
    <div class="row gx-5">
        <div class="col-lg-12">
            <div class="contact-form-area mb-70">
                <h1 class="text-center mb-4">Isi Jawaban Survey <?= $data['namasurvey'] ?></h1>
                <form method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td width="150px">Nama</td>
                                        <td><?= $data_akun['nama']; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="150px">Jenis Kelamin</td>
                                        <td><?= $data_akun['jekel']; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="150px">Usia</td>
                                        <td><?= $usia; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="150px">Pendidikan</td>
                                        <td><?= $data_akun['pendidikan']; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="150px">Pekerjaan</td>
                                        <td><?= $data_akun['pekerjaan']; ?></td>
                                    </tr>
                                    <tr>
                                        <td width="150px">Instansi</td>
                                        <td><?= $data_akun['instansi']; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php $nomor = 1;
                        $idsurvey = $_GET['id'];
                        $ambilrow = $conn->query("SELECT*FROM pertanyaan WHERE idsurvey='$idsurvey' order by idpertanyaan asc");
                        while ($data = $ambilrow->fetch_assoc()) {
                            $i = 1;
                        ?>
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
                                            <td>A. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" id="pilihan<?php echo $i . $data['idpertanyaan'] ?>" type="radio" value="A" required> <label for="pilihan<?php echo $i . $data['idpertanyaan'] ?>"><?php echo $data['a'];
                                                                                                                                                                                                                                                                    $i++ ?></label></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>B. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" id="pilihan<?php echo $i . $data['idpertanyaan'] ?>" type="radio" value="B"> <label for="pilihan<?php echo $i . $data['idpertanyaan'] ?>"><?php echo $data['b'];
                                                                                                                                                                                                                                                        $i++ ?></label></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>C. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" id="pilihan<?php echo $i . $data['idpertanyaan'] ?>" type="radio" value="C"> <label for="pilihan<?php echo $i . $data['idpertanyaan'] ?>"><?php echo $data['c'];
                                                                                                                                                                                                                                                        $i++ ?></label></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>D. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" id="pilihan<?php echo $i . $data['idpertanyaan'] ?>" type="radio" value="D"> <label for="pilihan<?php echo $i . $data['idpertanyaan'] ?>"><?php echo $data['d'];
                                                                                                                                                                                                                                                        $i++ ?></label></td>
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
<br><br>
<?php include('../layout/footer.php'); ?>
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
    $id_user = $_SESSION['akun']['id'];
    $nama = $data_akun['nama'];
    $jekel = $data_akun['jekel'];
    $usia = $usia;
    $pendidikan = $data_akun['pendidikan'];
    $pekerjaan = $data_akun['pekerjaan'];
    $instansi = $data_akun['instansi'];
    $idsurvey = $_POST['idsurvey'];
    $pilihan    = $_POST['pilihan'];
    $idpertanyaan    = $_POST['idpertanyaan'];
    $ambilsurvey = $conn->query("SELECT * FROM pertanyaan
    WHERE idsurvey='$idsurvey'");
    $jumlah = $ambilsurvey->num_rows;
    // echo $id_user;
    $conn->query("INSERT INTO jawaban
    	(id_user,nama,jekel,usia,pendidikan,pekerjaan,instansi,idsurvey)
    	VALUES('$id_user','$nama','$jekel','$usia','$pendidikan','$pekerjaan','$instansi','$idsurvey')") or die(mysqli_error($conn));
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

    $reward = $data_akun['reward'] + $data_survey['reward_per_orang'];
    $conn->query("UPDATE akun SET 
    reward = '$reward'
    WHERE id = '$id_user'");

    $temp_orang = $data_survey['temp_orang'] - 1;
    $conn->query("UPDATE survey SET 
    temp_orang = '$temp_orang'
    WHERE idsurvey='$_GET[id]'");

    $judul = $data_survey['namasurvey'];
    $jumlah_reward = $data_survey['reward_per_orang'];
    $conn->query("INSERT INTO history_point
    (id_user,idsurvey,deskripsi,jumlah)
    VALUES('$id_user','$_GET[id]','$judul','$jumlah_reward')");

    echo "<script>alert('Jawaban survey anda berhasil di simpan. Terima kasih telah berpastisipasi');</script>";
    echo "<script>location='kuesioner-list.php';</script>";
}
?>

</html