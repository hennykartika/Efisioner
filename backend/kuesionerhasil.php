<?php include 'header.php';
$idsiswa = $_SESSION['akun']['idsiswa'];
$ambil = $conn->query("SELECT * FROM survey WHERE idsurvey='$_GET[id]'");
$data = $ambil->fetch_assoc();
?>
<style>
    input[type="radio"]:disabled {
        -webkit-appearance: none;
        display: inline-block;
        width: 12px;
        height: 12px;
        padding: 0px;
        background-clip: content-box;
        border: 2px solid #bbbbbb;
        background-color: white;
        border-radius: 50%;
    }

    input[type="radio"]:checked {
        background-color: black;
    }
</style>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h2 class="page-title"> Hasil Jawaban Evaluasi </h2>
        </div>
        <div id="blog_page_area">
            <div class="container">
                <div class="row mb-5">
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
                                <h4>Hasil Jawaban Evaluasi</h4>
                                <br>
                                <?php
                                $ambiljawaban = $conn->query("SELECT * FROM jawaban left join siswa ON jawaban.idsiswa = siswa.idsiswa WHERE jawaban.idsiswa='$idsiswa' and idsurvey='$_GET[id]'");
                                $jawaban = $ambiljawaban->fetch_assoc();
                                ?>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td width="15%">Nama</td>
                                            <td>: <?= $jawaban['nama'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="15%">NIS</td>
                                            <td>: <?= $jawaban['nis'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Nilai</td>
                                            <td>: <?= $jawaban['nilai'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Benar</td>
                                            <td>: <?= $jawaban['benar'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Salah</td>
                                            <td>: <?= $jawaban['salah'] ?></td>
                                        </tr>
                                        <tr>
                                            <td width="15%">Waktu Submit</td>
                                            <td><?php echo tanggal(date('Y-m-d', strtotime($jawaban['waktu']))) . ' ' . date('H:i', strtotime($jawaban['waktu'])) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br>
                                <?php
                                $nomor = 1;
                                $ambildata = $conn->query("SELECT*FROM pertanyaan where idsurvey='$_GET[id]' order by idpertanyaan asc");
                                while ($data = $ambildata->fetch_assoc()) {
                                    $ambillistjawaban = $conn->query("SELECT * FROM listjawaban WHERE idjawaban='$jawaban[idjawaban]' and idpertanyaan='$data[idpertanyaan]'");
                                    $listjawaban = $ambillistjawaban->fetch_assoc();
                                ?>
                                    <input type="hidden" name="idpertanyaan[]" value="<?php echo $data['idpertanyaan']; ?>">
                                    <input type="hidden" name="idsurvey" value="<?php echo $data['idsurvey']; ?>">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <td width="5%"><?php echo $nomor ?>.</td>
                                                    <td><?php echo $data['pertanyaan']; ?></td>
                                                    <td width="10%"></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td>A. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" type="radio" <?php if ($listjawaban['jawaban'] == 'A') echo 'checked'; ?> value="A" disabled> <?php echo $data['a']; ?> </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td>B. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" type="radio" <?php if ($listjawaban['jawaban'] == 'B') echo 'checked'; ?> value="B" disabled> <?php echo $data['b']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td>C. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" type="radio" <?php if ($listjawaban['jawaban'] == 'C') echo 'checked'; ?> value="C" disabled> <?php echo $data['c']; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td>D. <input name="pilihan[<?php echo $data['idpertanyaan'] ?>]" type="radio" <?php if ($listjawaban['jawaban'] == 'D') echo 'checked'; ?> value="D" disabled> <?php echo $data['d']; ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                <?php
                                    $nomor++;
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include 'footer.php';
        ?>