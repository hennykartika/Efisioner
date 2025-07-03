<?php include('layout/header.php'); ?>

<?php
$id_user = isset($_SESSION['akun']['id']) == "" ? "" : $_SESSION['akun']['id'];
$data = $conn->query("SELECT survey.*, akun.* 
FROM survey
LEFT JOIN akun ON survey.id_user = akun.id
LEFT JOIN jawaban ON survey.idsurvey = jawaban.idsurvey AND jawaban.id_user = '$id_user'
WHERE jawaban.id_user IS NULL AND survey.id_user != '$id_user' AND survey.bayar = '1'
ORDER BY survey.idsurvey ASC");
?>

<style>
    .box {
        justify-content: center;
        background-color: #f9f9f9;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
        margin-bottom: 10px;
    }

    .box h3 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .box p {
        margin-bottom: 5px;
    }
</style>

<!-- Content Here -->
<section class="slider_section">
    <div class="slider_container">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active">
                    01
                </li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1">
                    02
                </li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2">
                    03
                </li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 px-0">
                                <div class="img-box">
                                    <img src="<?= base_url() ?>assets/images/iklan.png" alt="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-box">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 px-0">
                                <div class="img-box">
                                    <img src="<?= base_url() ?>assets/images/iklan2.png" alt="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-box">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12 px-0">
                                <div class="img-box">
                                    <img src="<?= base_url() ?>assets/images/iklan3.png" alt="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-box">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel_btn-box">
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="sr-only">Previous</span>
                </a>
                <img src="images/line.png" alt="" />
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>
</section>
<section class="service_section layout_padding">
    <div class="container">
        <div class="heading_container">
            <h2 style="color: #0077B6;">
                KUESIONER
            </h2>
        </div>
        <?php
        while ($row = $data->fetch_assoc()) { ?>
            <div class="row my-2">
                <div class="col-md-12">
                    <div class="box col-md-10 border d-flex align-items-center p-3">
                        <div>
                            <h3><?= $row['namasurvey'] ?></h3>
                            <p class="text-muted"><?= $row['nama'] ?></p>
                            <div>
                                <p><strong style="color: green;">Reward : Rp <?= number_format($row['reward_per_orang'], 0, ',', '.') ?></strong></p>
                                <p class="text-muted"><?= $row['isi'] ?></p>
                            </div>
                        </div>
                        <div class="ml-auto">
                            <a href="kuesioner/kuesioner-survey.php?id=<?= $row['idsurvey'] ?>" class="btn btn-primary">Klik Disini</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
        if ($data->num_rows == 0) {
        ?>
            <hr>
            <center>
                <h4>Tidak ada kuesioner</h4>
            </center>
            <hr>
        <?php
        }
        ?>
    </div>
</section>


<?php include('layout/footer.php'); ?>