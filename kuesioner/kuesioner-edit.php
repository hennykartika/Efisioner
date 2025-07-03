<?php include '../layout/header.php';
if (!isset($_SESSION['akun'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!')</script>";
    echo "<script>location='../login.php';</script>";
    exit();
}
$idsurvey = $_GET['id'];
$ambil = $conn->query("SELECT * FROM survey WHERE idsurvey='$_GET[id]'");
$data = $ambil->fetch_assoc();
?>
<hr>
<div class="container mt-3">
    <div class="content-wrapper">
        <h3 class="page-heading mb-4 text-center">Edit Survey</h3>
        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Survey</label>
                        <input type="text" class="form-control" name="namasurvey" value="<?= $data['namasurvey'] ?>" placeholder="Nama Survey" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" name="isi" id="isi" rows="10"><?= $data['isi'] ?></textarea>
                        <script>
                            CKEDITOR.replace('isi');
                        </script>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Target Responden Orang</label>
                        <input type="number" value="<?= $data['jumlah_orang'] ?>" class="form-control" name="jumlah_orang" placeholder="Jumlah Target Responden Orang" required>
                    </div>
                    <div class="form-group">
                        <label>Bayar Per Orang</label>
                        <input type="number" value="<?= $data['reward_per_orang'] ?>" class="form-control" name="reward_per_orang" placeholder="Bayar Per Orang" required>
                    </div>
                    <h3 id="total_bayar">Total Bayar : </h3>
                    <div class="form-group">
                        <a href="kuesioner-bayar.php?id=<?= $idsurvey ?>" class="btn btn-success float-right pull-right ml-2">Bayar</a>
                        <button class="btn btn-primary float-right pull-right" type="submit" name="simpan" value="simpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <h4>Tambah Pertanyaan</h4>
                <br>
                <a href="#" data-toggle="modal" data-target="#tambahsurvey" class="btn btn-primary">Tambah Pertanyaan</a>
                <div class="modal fade" id="tambahsurvey" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Tambah Pertanyaan Survey</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="survey"><span class="text-danger">*</span>Pertanyaan Survey</label>
                                        <textarea name="pertanyaan" value="" class="form-control" id="pertanyaan" rows="3"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="survey"><span class="text-danger">*</span>Jawaban A</label>
                                                <input type="text" name="a" value="" class="form-control" id="a" />
                                            </div>
                                            <div class="form-group">
                                                <label for="survey"><span class="text-danger">*</span>Jawaban B</label>
                                                <input type="text" name="b" value="" class="form-control" id="b" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="survey"><span class="text-danger">*</span>Jawaban C</label>
                                                <input type="text" name="c" value="" class="form-control" id="c" />
                                            </div>
                                            <div class="form-group">
                                                <label for="survey"><span class="text-danger">*</span>Jawaban D</label>
                                                <input type="text" name="d" value="" class="form-control" id="d" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary float-right pull-right" type="submit" name="tambah" value="tambah">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tabel">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pertanyaan</th>
                                <th>A</th>
                                <th>B</th>
                                <th>C</th>
                                <th>D</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $nomor = 1;
                            $ambilrow = $conn->query("SELECT*FROM pertanyaan where idsurvey = '$idsurvey' order by idpertanyaan asc");
                            while ($data = $ambilrow->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $nomor; ?></td>
                                    <td><?php echo $data['pertanyaan'] ?></td>
                                    <td><?php echo $data['a'] ?></td>
                                    <td><?php echo $data['b'] ?></td>
                                    <td><?php echo $data['c'] ?></td>
                                    <td><?php echo $data['d'] ?></td>
                                    <td>
                                        <a href="#" data-toggle="modal" data-target="#edit<?= $nomor ?>" class="btn btn-warning btn-sm m-1">Edit Pertanyaan</a>
                                        <a href="pertanyaanhapus.php?id=<?= $data['idpertanyaan'] ?>&idsurvey=<?= $data['idsurvey'] ?>" class="btn btn-danger btn-sm m-1" onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')">Hapus</a>
                                    </td>
                                </tr>
                                <div class="modal fade" id="edit<?= $nomor ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Edit Pertanyaan Survey</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <label for="survey"><span class="text-danger">*</span>Pertanyaan <?= $nomor ?></label>
                                                        <textarea name="pertanyaan" value="" class="form-control" id="pertanyaan<?= $nomor ?>" rows="3"><?= $data['pertanyaan'] ?></textarea>
                                                        <input type="hidden" name="idpertanyaan" class="form-control" value="<?= $data['idpertanyaan'] ?>">
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="survey"><span class="text-danger">*</span>Jawaban A</label>
                                                                <input type="text" name="a" value="<?= $data['a'] ?>" class="form-control" id="a" />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="survey"><span class="text-danger">*</span>Jawaban B</label>
                                                                <input type="text" name="b" value="<?= $data['b'] ?>" class="form-control" id="b" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="survey"><span class="text-danger">*</span>Jawaban C</label>
                                                                <input type="text" name="c" value="<?= $data['c'] ?>" class="form-control" id="c" />
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="survey"><span class="text-danger">*</span>Jawaban D</label>
                                                                <input type="text" name="d" value="<?= $data['d'] ?>" class="form-control" id="d" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button class="btn btn-primary float-right pull-right" type="submit" name="edit" value="edit">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                $nomor++;
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if (isset($_POST['simpan'])) {
    $reward_per_orang = $_POST['reward_per_orang'];
    $jumlah_orang = $_POST['jumlah_orang'];
    $harga = $reward_per_orang * $jumlah_orang + 1000;
    $conn->query("UPDATE survey SET namasurvey='$_POST[namasurvey]',isi='$_POST[isi]',reward_per_orang='$_POST[reward_per_orang]',jumlah_orang='$_POST[jumlah_orang]',harga='$_POST[harga]',updated_at=NOW() WHERE idsurvey='$_GET[id]'") or die(mysqli_error($conn));
    echo "<script>alert('Data Berhasil Di Edit');</script>";
    echo "<script>location='kuesioner-edit.php?id=$_GET[id]';</script>";
}
if (isset($_POST['tambah'])) {
    $conn->query("INSERT INTO pertanyaan
		(idsurvey,pertanyaan,a,b,c,d)
		VALUES('$_GET[id]','$_POST[pertanyaan]','$_POST[a]','$_POST[b]','$_POST[c]','$_POST[d]')") or die(mysqli_error($conn));
    echo "<script>alert('Data Berhasil Di Simpan');</script>";
    echo "<script>location='kuesioner-edit.php?id=$_GET[id]';</script>";
}
if (isset($_POST['edit'])) {
    $conn->query("UPDATE pertanyaan SET pertanyaan='$_POST[pertanyaan]',a='$_POST[a]',b='$_POST[b]',c='$_POST[c]', d='$_POST[d]' WHERE idpertanyaan='$_POST[idpertanyaan]'") or die(mysqli_error($conn));
    echo "<script>alert('Data Berhasil Di Edit');</script>";
    echo "<script>location='kuesioner-edit.php?id=$_GET[id]';</script>";
}
include '../layout/footer.php';
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var jumlahOrangInput = document.querySelector('input[name="jumlah_orang"]');
        var bayarInput = document.querySelector('input[name="reward_per_orang"]');
        var totalBayarElement = document.querySelector('h3#total_bayar');

        function hitungTotalBayar() {
            var jumlahOrang = jumlahOrangInput.value;
            var bayarPerOrang = bayarInput.value;

            var totalBayar = (jumlahOrang * bayarPerOrang) + 1000;

            // Memformat angka menjadi format mata uang Rupiah
            var formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });
            var totalBayarFormatted = formatter.format(totalBayar);

            totalBayarElement.textContent = 'Total Bayar + Biaya Admin (Rp 1.000) : ' + totalBayarFormatted;
        }

        jumlahOrangInput.addEventListener('input', hitungTotalBayar);
        bayarInput.addEventListener('input', hitungTotalBayar);

        hitungTotalBayar();
    });
</script>