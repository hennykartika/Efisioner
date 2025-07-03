<?php
include('../layout/header.php');
if (!isset($_SESSION['akun'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!')</script>";
    echo "<script>location='../login.php';</script>";
    exit();
}
?>

<!-- Content Here -->
<section class="agency_section layout_padding2-top">
    <div class="agency_container ">
        <div class="box" style="width: 90%;">
            <div class="detail-box">
                <div class="heading_container">
                    <h2>
                        EFISIONER
                    </h2>
                </div>
                <p>
                    Buat Kuesioner penelitianmu! <br>Dapatkan Jumlah Responden Sesuai Targetmu
                </p>
                <a href="<?= base_url("kuesioner/kuesioner-ajukan") ?>">
                    Buat Kuesioner
                </a>
                <a href="<?= base_url("kuesioner/kuesioner-list") ?>">
                    List Kuesioner
                </a>
                <a href="<?= base_url("kuesioner/kuesioner-anda") ?>">
                    Hasil Kuesioner Anda
                </a>
            </div>
        </div>
    </div>
</section>

<?php include('../layout/footer.php'); ?>