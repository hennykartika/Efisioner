<?php
include('../layout/header.php');
if (!isset($_SESSION['akun'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!')</script>";
    echo "<script>location='../login.php';</script>";
    exit();
}

$id = $_SESSION['akun']['id'];
$ambil = $conn->query("SELECT * FROM akun WHERE id='$id'");
$data = $ambil->fetch_assoc();
?>
<style>
    .breadcrumb {
        background-color: #c5cee4;

    }

    .breadcrumb a {
        text-decoration: none;
    }

    .container {
        max-width: 1000px;
        padding: 0;
    }

    p {
        margin: 0;
    }

    .d-flex a {
        text-decoration: none;
        color: #686868;
    }

    img.photo {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
    }

    .fab.fa-twitter {
        color: #8ab7f1;
    }

    .fab.fa-instagram {
        color: #E1306C;
    }

    .fab.fa-facebook-f {
        color: #5999ec;
    }
</style>
<!-- Content Here -->
<hr>
<div class="container rounded bg-white mt-5 mb-5">
    <div class="row">
        <div class="col-md-3 border-right">
            <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                <img class="rounded-circle mt-5" width="150px" src="https://www.its.ac.id/international/wp-content/uploads/sites/66/2020/02/blank-profile-picture-973460_1280.jpg">
                <span class="font-weight-bold"><?= $data['nama'] ?></span>
                <span class="text-black-50"><?= $data['email'] ?></span>
                <span> </span>
            </div>
        </div>
        <div class="col-md-9 border-right">
            <div class="p-3 py-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-right">Edit Profil</h4>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="row mt-3">
                        <div class="row ml-1 mb-2 w-100">
                            <div class="col-md-2"><label class="labels">Nama</label></div>
                            <div class="col-md-10"><input type="text" name="nama" class="form-control" placeholder="Nama" value="<?= $data['nama'] ?>"></div>
                        </div>
                        <div class="row ml-1 mb-2 w-100">
                            <div class="col-md-2"><label class="labels">Email</label></div>
                            <div class="col-md-10"><input type="email" name="email" class="form-control" placeholder="Email" value="<?= $data['email'] ?>"></div>
                        </div>
                        <div class="row ml-1 mb-2 w-100">
                            <div class="col-md-2"><label class="labels">No Hp</label></div>
                            <div class="col-md-10"><input type="text" name="nohp" class="form-control" placeholder="No Hp" value="<?= $data['nohp'] ?>"></div>
                        </div>
                        <div class="row ml-1 mb-2 w-100">
                            <div class="col-md-2"><label class="labels">Alamat</label></div>
                            <div class="col-md-10">
                                <textarea name="alamat" class="form-control bg-light border-0" placeholder="Alamat" style="height: 120px;"><?= $data['alamat'] ?></textarea>
                            </div>
                        </div>
                        <div class="row ml-1 mb-2 w-100">
                            <div class="col-md-2"><label class="labels">Username</label></div>
                            <div class="col-md-10"><input type="text" name="username" class="form-control" placeholder="Username" readonly value="<?= $data['username'] ?>"></div>
                        </div>
                        <div class="row ml-1 mb-2 w-100">
                            <div class="col-md-2"><label class="labels">Password</label></div>
                            <div class="col-md-10"><input type="password" name="password" class="form-control" placeholder="Password" value="<?= $data['password'] ?>"></div>
                        </div>
                        <div class="row ml-1 mb-2 w-100">
                            <div class="col-md-2"><label class="labels">Tanggal Lahir</label></div>
                            <div class="col-md-3"><input type="date" name="tanggal_lahir" class="form-control" placeholder="Tanggal Lahir" value="<?= $data['tanggal_lahir'] ?>"></div>
                            <div class="col-md-3"><label class="labels">Jenis Kelamin</label></div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="radio1" name="jekel" value="Laki-laki" <?= $data['jekel'] == 'Laki-laki' ? 'checked' : '' ?> class="custom-control-input">
                                <label class="custom-control-label" for="radio1">Laki-laki</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="radio2" name="jekel" value="Perempuan" <?= $data['jekel'] == 'Perempuan' ? 'checked' : '' ?> class="custom-control-input">
                                <label class="custom-control-label" for="radio2">Perempuan</label>
                            </div>
                        </div>
                        <div class="row ml-1 mb-2 w-100">
                            <div class="col-md-2"><label class="labels">Pendidikan</label></div>
                            <div class="col-md-10">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radioSD" name="pendidikan" value="SD" class="custom-control-input" <?= $data['pendidikan'] == 'SD' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="radioSD">SD</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radioSMP" name="pendidikan" value="SMP" class="custom-control-input" <?= $data['pendidikan'] == 'SMP' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="radioSMP">SMP</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radioSMA" name="pendidikan" value="SMA" class="custom-control-input" <?= $data['pendidikan'] == 'SMA' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="radioSMA">SMA</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radio4" name="pendidikan" value="D3" class="custom-control-input" <?= $data['pendidikan'] == 'D3' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="radio4">D3</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radio5" name="pendidikan" value="D4" class="custom-control-input" <?= $data['pendidikan'] == 'D4' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="radio5">D4</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radio6" name="pendidikan" value="S1" class="custom-control-input" <?= $data['pendidikan'] == 'S1' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="radio6">S1</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radio7" name="pendidikan" value="S2" class="custom-control-input" <?= $data['pendidikan'] == 'S2' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="radio7">S2</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radio8" name="pendidikan" value="S3" class="custom-control-input" <?= $data['pendidikan'] == 'S3' ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="radio8">S3</label>
                                </div>
                            </div>
                        </div>
                        <div class="row ml-1 mb-2 w-100">
                            <div class="col-md-2"><label class="labels">Pekerjaan</label></div>
                            <div class="col-md-10">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radioPNS" name="pekerjaan" value="PNS" class="custom-control-input" <?= $data['pekerjaan'] === 'PNS' ? 'checked' : '' ?> required>
                                    <label class="custom-control-label" for="radioPNS">PNS</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radioTNI" name="pekerjaan" value="TNI" class="custom-control-input" <?= $data['pekerjaan'] === 'TNI' ? 'checked' : '' ?> required>
                                    <label class="custom-control-label" for="radioTNI">TNI</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radioPolri" name="pekerjaan" value="Polri" class="custom-control-input" <?= $data['pekerjaan'] === 'Polri' ? 'checked' : '' ?> required>
                                    <label class="custom-control-label" for="radioPolri">Polri</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radioSwasta" name="pekerjaan" value="Swasta" class="custom-control-input" <?= $data['pekerjaan'] === 'Swasta' ? 'checked' : '' ?> required>
                                    <label class="custom-control-label" for="radioSwasta">Swasta</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="radioWirausaha" name="pekerjaan" value="Wirausaha" class="custom-control-input" <?= $data['pekerjaan'] === 'Wirausaha' ? 'checked' : '' ?> required>
                                    <label class="custom-control-label" for="radioWirausaha">Wirausaha</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" name="pekerjaan" value="Lainnya" id="lainnya" class="custom-control-input" <?= $data['pekerjaan'] != 'PNS' && $data['pekerjaan'] != 'TNI' && $data['pekerjaan'] != 'Polri' && $data['pekerjaan'] != 'Swasta' && $data['pekerjaan'] != 'Wirausaha' ? 'checked' : '' ?> required>
                                    <label class="custom-control-label" for="lainnya">Lainnya (sebutkan)</label>
                                </div>
                                <br>
                                <input type="text" id="pekerjaaninput" name="pekerjaaninput" value="<?= $data['pekerjaan'] != 'PNS' && $data['pekerjaan'] != 'TNI' && $data['pekerjaan'] != 'Polri' && $data['pekerjaan'] != 'Swasta' && $data['pekerjaan'] != 'Wirausaha' ? $data['pekerjaan'] : '' ?>" class="form-control bg-light border-0" placeholder="Masukkan Pekerjaan" style="height: 55px; <?= $data['pekerjaan'] != 'PNS' && $data['pekerjaan'] != 'TNI' && $data['pekerjaan'] != 'Polri' && $data['pekerjaan'] != 'Swasta' && $data['pekerjaan'] != 'Wirausaha' ? '' : 'display: none;' ?>">
                            </div>
                        </div>
                        <div class="row ml-1 mb-2 w-100">
                            <div class="col-md-2"><label class="labels">Instansi</label></div>
                            <div class="col-md-10"><input type="text" name="instansi" class="form-control" placeholder="Instansi" value="<?= $data['instansi'] ?>"></div>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <a class="btn btn-info" href="tampil.php">Kembali ke Profil</a>
                        <button class="btn btn-primary profile-button" type="submit" name="save" value="save">Save Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php'); ?>

<script>
    $(document).ready(function() {
        const lainnyaRadio = $('#lainnya');
        const pekerjaanInput = $('#pekerjaaninput');

        lainnyaRadio.on('click', function() {
            if ($(this).is(':checked')) {
                pekerjaanInput.show();
            } else {
                pekerjaanInput.hide();
                pekerjaanInput.show();
            }
        });

        $('input[name="pekerjaan"]:not(#lainnya)').on('click', function() {
            pekerjaanInput.hide();
        });
    });
</script>

<?php
if (isset($_POST["save"])) {
    $nama = $_POST["nama"];
    $email = $_POST["email"];
    $nohp = $_POST["nohp"];
    $alamat = $_POST["alamat"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $jekel = $_POST["jekel"];
    $tanggal_lahir = $_POST["tanggal_lahir"];
    $pendidikan = $_POST["pendidikan"];
    $pekerjaan = ($_POST["pekerjaan"] == "Lainnya") ? $_POST["pekerjaaninput"] : $_POST["pekerjaan"];
    $instansi = $_POST["instansi"];

    $conn->query("UPDATE akun SET 
    nama = '$nama',
    email = '$email',
    password = '$password',
    nohp = '$nohp',
    alamat = '$alamat',
    username = '$username',
    jekel = '$jekel',
    tanggal_lahir = '$tanggal_lahir',
    pendidikan = '$pendidikan',
    pekerjaan = '$pekerjaan',
    instansi = '$instansi',
    updated_at=NOW()
    WHERE id = '$id'");
    $akunyangcocok = $conn->affected_rows;
    if ($akunyangcocok >= 1) {
        echo "<script> alert('Edit Berhasil');</script>";
        echo "<script> location ='tampil.php';</script>";
    } else {
        echo "<script> alert('Edit Gagal');</script>";
        echo "<script> location ='profil-edit.php';</script>";
    }
}
?>