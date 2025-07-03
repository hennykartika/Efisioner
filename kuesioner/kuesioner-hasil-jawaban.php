<?php
include '../layout/header.php';
$idjawaban = $_GET['id'];
$ambil = $conn->query("SELECT * FROM survey WHERE idsurvey='$_GET[idsurvey]'");
$data = $ambil->fetch_assoc();
?>
<hr>
<div class="content-wrapper">
    <center>
        <h3 class="page-heading mb-2">Jawaban Kuesioner <?= $data['namasurvey'] ?></h3>
        <a target="_blank" href="kuesioner-hasil.php?id=<?= $_GET['idsurvey'] ?>" class="btn btn-info text-white" style="margin-top:25px">Kembali Ke Hasil</a>
        <br><br>
    </center>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td width="15%">Nama Kuesioner</td>
                            <td><?= $data['namasurvey'] ?></td>
                        </tr>
                        <tr>
                            <td width="15%">Deskripsi</td>
                            <td><?= $data['isi'] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            <?php
            $ambiljawaban = $conn->query("SELECT * FROM jawaban WHERE idjawaban='$idjawaban' and idsurvey='$_GET[idsurvey]'");
            $jawaban = $ambiljawaban->fetch_assoc();
            ?>
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td width="150px">Nama</td>
                        <td><?= $jawaban['nama']; ?>
                    </tr>
                    <tr>
                        <td width="150px">Jenis Kelamin</td>
                        <td><?= $jawaban['jekel']; ?>
                    </tr>
                    <tr>
                        <td width="150px">Usia</td>
                        <td><?= $jawaban['usia']; ?>
                    </tr>
                    <tr>
                        <td width="150px">Pendidikan</td>
                        <td><?= $jawaban['pendidikan']; ?>
                    </tr>
                    <tr>
                        <td width="150px">Pekerjaan</td>
                        <td><?= $jawaban['pekerjaan']; ?>
                    </tr>
                    <tr>
                        <td width="150px">Instansi</td>
                        <td><?= $jawaban['instansi']; ?>
                    </tr>
                    <tr>
                        <td width="150px">Waktu Submit</td>
                        <td><?php echo tanggal(date('Y-m-d', strtotime($jawaban['waktu']))) . ' ' . date('H:i', strtotime($jawaban['waktu'])) ?></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <?php
            $nomor = 1;
            $ambildata = $conn->query("SELECT*FROM pertanyaan where idsurvey='$_GET[idsurvey]' order by idpertanyaan asc");
            while ($data = $ambildata->fetch_assoc()) {
                $ambillistjawaban = $conn->query("SELECT * FROM listjawaban WHERE idjawaban='$jawaban[idjawaban]' and idpertanyaan='$data[idpertanyaan]'");
                $listjawaban = $ambillistjawaban->fetch_assoc();
            ?>
                <input type="hidden" name="idpertanyaan[]" value="<?php echo $data['idpertanyaan']; ?>">
                <input type="hidden" name="idsurvey" value="<?php echo $data['idsurvey']; ?>">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td width="5%"><?php echo $nomor ?>.</td>
                                <td><?php echo $data['pertanyaan']; ?></td>
                            </tr>
                            <tr class="<?php if ($listjawaban['jawaban'] == 'A') echo 'bg-success text-white'; ?>">
                                <td></td>
                                <td>A. <?php echo $data['a']; ?></td>
                            </tr>
                            <tr class="<?php if ($listjawaban['jawaban'] == 'B') echo 'bg-success text-white'; ?>">
                                <td></td>
                                <td>B. <?php echo $data['b']; ?></td>
                            </tr>
                            <tr class="<?php if ($listjawaban['jawaban'] == 'C') echo 'bg-success text-white'; ?>">
                                <td></td>
                                <td>C. <?php echo $data['c']; ?></td>
                            </tr>
                            <tr class="<?php if ($listjawaban['jawaban'] == 'D') echo 'bg-success text-white'; ?>">
                                <td></td>
                                <td>D. <?php echo $data['d']; ?></td>
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
<?php
include '../layout/footer.php';
?>