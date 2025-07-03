<?php
include 'header.php';
$idsurvey = $_GET['id'];
$ambil = $conn->query("SELECT * FROM survey WHERE idsurvey='$_GET[id]'");
$data = $ambil->fetch_assoc();
?>
<div class="content-wrapper">
    <h3 class="page-heading mb-4">Data Penjawab Kuesioner <?= $data['namasurvey'] ?></h3>
    <div class="card">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Kuesioner</label>
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
    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tabel">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Usia</th>
                            <th>Pendidikan</th>
                            <th>Pekerjaan</th>
                            <th>Instansi</th>
                            <th>Waktu Submit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1;
                        $ambildata = $conn->query("SELECT*FROM jawaban where idsurvey = '$idsurvey'");
                        while ($data = $ambildata->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $nomor; ?></td>
                                <td><?php echo $data['nama'] ?></td>
                                <td><?php echo $data['usia'] ?></td>
                                <td><?php echo $data['pendidikan'] ?></td>
                                <td><?php echo $data['pekerjaan'] ?></td>
                                <td><?php echo $data['instansi'] ?></td>
                                <td><?php echo tanggal(date('Y-m-d', strtotime($data['waktu']))) . ' ' . date('H:i', strtotime($data['waktu'])) ?></td>
                                <td>
                                    <a href="kuesionerhasiljawaban.php?id=<?php echo $data['idjawaban']; ?>&idsurvey=<?php echo $_GET['id']; ?>" class="btn btn-success btn-sm m-1">Jawaban</a>
                                    <a href="jawabanhapus.php?id=<?php echo $data['idjawaban']; ?>&idsurvey=<?php echo $_GET['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')">Hapus</a>
                                </td>
                            </tr>
                        <?php
                            $nomor++;
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
include 'footer.php';
?>