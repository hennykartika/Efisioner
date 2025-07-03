<?php
include 'header.php';
?>
<div class="content-wrapper">
    <h3 class="page-heading mb-4">Kuesioner Tambah</h3>
    <div class="card">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Kuesioner</label>
                    <input type="text" class="form-control" name="namasurvey" placeholder="Nama Survey" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea class="form-control" name="isi" id="isi" rows="10"></textarea>
                    <script>
                        CKEDITOR.replace('isi');
                    </script>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary float-right pull-right" type="submit" name="simpan" value="simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
if (isset($_POST['simpan'])) {
    $conn->query("INSERT INTO survey
		(namasurvey,isi)
		VALUES('$_POST[namasurvey]','$_POST[isi]')") or die(mysqli_error($conn));
    $idkuesioner = $conn->insert_id;
    echo "<script>alert('Data Berhasil Di Simpan');</script>";
    echo "<script> location ='surveyedit.php?id=$idkuesioner';</script>";
}
?>
<?php
include 'footer.php';
?>