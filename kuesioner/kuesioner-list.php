<?php
include('../layout/header.php');
if (!isset($_SESSION['akun'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!')</script>";
    echo "<script>location='../login.php';</script>";
    exit();
}
$id_user = $_SESSION['akun']['id'];
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
        /* width: 600px; */
        /* padding: 20px; */
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
<hr>
<div class="container">
    <center>
        <h3>Pilih Kuesioner</h3>
    </center>
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
                        <a href="kuesioner-survey.php?id=<?= $row['idsurvey'] ?>" class="btn btn-primary">Klik Disini</a>
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








<?php include('../layout/footer.php'); ?>