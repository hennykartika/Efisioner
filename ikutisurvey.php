<?php include 'head.php' ?>
<?php
$ambil = $conn->query("SELECT * FROM survey WHERE idsurvey='$_GET[id]'");
$data = $ambil->fetch_assoc();
?>
<div class="container">
    <div class="row gx-5">
        <div class="col-lg-12">
            <div class="contact-form-area mb-70">
                <h1 class="text-center mb-4">Isi Formulir <?= $data['namasurvey'] ?></h1>
                <form action="" method="post">
                    <div class="row g-3">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <b><label>Nama</label></b>
                                <input type="hidden" name="idsurvey" value="<?= $_GET['id'] ?>">
                                <input type="text" name="nama" value="" class="form-control bg-light border-0" placeholder="Nama Lengkap" style="height: 55px;" required>
                            </div>
                            <div class="form-group">
                                <b><label>Usia</label></b>
                                <input type="number" name="usia" min="1" max="99" value="" class="form-control bg-light border-0" placeholder="Usia" style="height: 55px;" required>
                            </div>
                            <div class="form-group">
                                <b><label>Jenis Layanan Yang Di Terima</label></b>
                                <input type="text" name="jenislayanan" value="" class="form-control bg-light border-0" placeholder="Layanan" style="height: 55px;" required>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <b><label>Jenis Kelamin</label></b>
                                <br>
                                <input type="radio" name="jekel" value="Laki - Laki" required>
                                <label>Laki - Laki</label>
                                <input type="radio" name="jekel" value="Perempuan" required>
                                <label>Perempuan</label>
                            </div>
                            <div class="form-group">
                                <b><label>Pendidikan</label></b>
                                <br>
                                <input type="radio" name="pendidikan" value="SD" required>
                                <label>SD</label>
                                <input type="radio" name="pendidikan" value="SMP" required>
                                <label>SMP</label>
                                <input type="radio" name="pendidikan" value="SMA" required>
                                <label>SMA</label>
                                <input type="radio" name="pendidikan" value="D3" required>
                                <label>D3</label>
                                <input type="radio" name="pendidikan" value="D4" required>
                                <label>D4</label>
                                <input type="radio" name="pendidikan" value="S1" required>
                                <label>S1</label>
                                <input type="radio" name="pendidikan" value="S2" required>
                                <label>S2</label>
                                <input type="radio" name="pendidikan" value="S3" required>
                                <label>S3</label>
                            </div>
                            <div class="form-group">
                                <b><label>Pekerjaan</label></b>
                                <br>
                                <input type="radio" name="pekerjaan" value="PNS" required>
                                <label>PNS</label>
                                <input type="radio" name="pekerjaan" value="TNI" required>
                                <label>TNI</label>
                                <input type="radio" name="pekerjaan" value="Polri" required>
                                <label>Polri</label>
                                <input type="radio" name="pekerjaan" value="Swasta" required>
                                <label>Swasta</label>
                                <input type="radio" name="pekerjaan" value="Wirausaha" required>
                                <label>Wirausaha</label>
                                <input type="radio" name="pekerjaan" value="Lainnya" id="lainnya" required>
                                <label>Lainnya (sebutkan)</label><br>
                                <input type="text" name="pekerjaaninput" id="pekerjaaninput" value="" class="form-control bg-light border-0" placeholder="Masukkan Pekerjaan" style="height: 55px;display:none">
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-dark w-100 py-3" name="simpan" value="simpan" type="submit">Jawab Survey <?= $data['namasurvey'] ?></button>
                        </div>
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
if (isset($_POST["simpan"])) {
    $_SESSION['formulir']['nama'] = $_POST["nama"];
    $_SESSION['formulir']['jekel'] = $_POST["jekel"];
    $_SESSION['formulir']['usia'] = $_POST["usia"];
    $_SESSION['formulir']['pendidikan'] = $_POST["pendidikan"];
    $_SESSION['formulir']['pekerjaan'] = $_POST["pekerjaan"];
    if ($_POST['pekerjaan'] == "Lainnya") {
        $_SESSION['formulir']['pekerjaan'] = $_POST["pekerjaaninput"];
    } else {
        $_SESSION['formulir']['pekerjaan'] = $_POST["pekerjaan"];
    }
    $_SESSION['formulir']['jenislayanan'] = $_POST["jenislayanan"];
    $_SESSION['formulir']['idsurvey'] = $_POST["idsurvey"];
    echo "<script> location ='jawab.php';</script>";
}
?>

</html