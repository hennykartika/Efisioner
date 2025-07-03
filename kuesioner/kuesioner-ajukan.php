<?php
include '../layout/header.php';
if (!isset($_SESSION['akun'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!')</script>";
    echo "<script>location='../login.php';</script>";
    exit();
}
?>
<hr>
<div class="container mt-3">
    <div class="content-wrapper">
        <h3 class="page-heading mb-4 text-center">Kuesioner Tambah</h3>
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
                        <label>Jumlah Seluruh Target Responden </label>
                        <input type="number" class="form-control" name="jumlah_orang" placeholder="Jumlah Target Responden Orang" required>
                    </div>
                    <div class="form-group">
                        <label>Reward Yang Di Dapatkan Per Orang Responden</label>
                        <input type="number" class="form-control" name="reward_per_orang" placeholder="Reward Yang Di Dapatkan Per Orang Responden" required>
                    </div>
                    <h3 id="total_bayar">Total Pembayaran : </h3>
                    <div class="form-group">
                        <button class="btn btn-primary float-right pull-right" type="submit" name="simpan" value="simpan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
if (isset($_POST['simpan'])) {
    $id = $_SESSION["akun"]["id"];
    $reward_per_orang = $_POST['reward_per_orang'];
    $jumlah_orang = $_POST['jumlah_orang'];
    $harga = $reward_per_orang * $jumlah_orang + 1000;

    $conn->query("INSERT INTO survey
		(id_user,namasurvey,isi,reward_per_orang,jumlah_orang,harga,temp_orang)
		VALUES('$id', '$_POST[namasurvey]','$_POST[isi]','$reward_per_orang','$jumlah_orang','$harga','$jumlah_orang')") or die(mysqli_error($conn));
    $idkuesioner = $conn->insert_id;
    echo "<script>alert('Data Berhasil Di Simpan');</script>";
    echo "<script> location ='kuesioner-edit.php?id=$idkuesioner';</script>";
}
?>
<?php
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
    });
</script>