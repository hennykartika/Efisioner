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
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-12 bg-white p-0 px-3 py-3 mb-3">
                    <div class="d-flex flex-column align-items-center">
                        <img class="photo" src="https://www.its.ac.id/international/wp-content/uploads/sites/66/2020/02/blank-profile-picture-973460_1280.jpg" alt="">
                        <p class="fw-bold h4 mt-3"><?= $data['nama'] ?></p>
                        <div class="d-flex ">
                            <a href="profil-edit.php">
                                <div class="btn btn-primary follow me-2">Edit Profil</div>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-7 ps-md-4">
            <div class="row">
                <div class="col-12 bg-white px-3 mb-3 pb-3">
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2">Nama</p>
                        <p class="py-2 text-muted"><?= $data['nama'] ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2">Email</p>
                        <p class="py-2 text-muted"><?= $data['email'] ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2">No Hp</p>
                        <p class="py-2 text-muted"><?= $data['nohp'] ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2">Username</p>
                        <p class="py-2 text-muted"><?= $data['username'] ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2">Alamat</p>
                        <p class="py-2 text-muted"><?= $data['alamat'] ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2">Tanggal Lahir</p>
                        <p class="py-2 text-muted"><?= $data['tanggal_lahir'] ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2">Jenis Kelamin</p>
                        <p class="py-2 text-muted"><?= $data['jekel'] ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2">Pendidikan</p>
                        <p class="py-2 text-muted"><?= $data['pendidikan'] ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2">Pekerjaan</p>
                        <p class="py-2 text-muted"><?= $data['pekerjaan'] ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">
                        <p class="py-2">Instansi</p>
                        <p class="py-2 text-muted"><?= $data['instansi'] ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between border-bottom">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php'); ?>