<?php include 'header.php';
?>
<style>
    .card-img-overlay {
        background-color: rgba(#000, 0.4);
    }
</style>
<div class="content-wrapper">
    <h3 class="page-heading mb-4">Kuesioner Daftar</h3>
    <div class="card">
        <div class="card-body">
            <a href="surveytambah.php" class="btn btn-primary">Tambah Kuesioner</a>
            <br>
            <br>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tabel">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kuesioner</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1;
                        $ambilrow = $conn->query("SELECT*FROM survey order by idsurvey desc");
                        while ($data = $ambilrow->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $nomor; ?></td>
                                <td><?php echo $data['namasurvey'] ?> </td>
                                <td>
                                    <a href="surveypenjawab.php?id=<?php echo $data['idsurvey']; ?>" class="btn btn-success btn-sm m-1">Jawaban</a>
                                    <a href="surveyedit.php?id=<?php echo $data['idsurvey']; ?>" class="btn btn-warning btn-sm m-1">Edit</a>
                                    <a href="surveyhapus.php?id=<?php echo $data['idsurvey']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Ini ?')">Hapus</a>
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