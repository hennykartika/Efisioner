<?php
include 'header.php';
if (!isset($_SESSION["akun"])) {
    echo "<script> alert('Harap login terlebih dahulu');</script>";
    echo "<script> location ='login.php';</script>";
}
$id = $_SESSION["akun"]['id'];
$ambil = $conn->query("SELECT *FROM akun WHERE id='$id'");
$pecah = $ambil->fetch_assoc(); ?>
<div class="content-wrapper">
    <h3 class="page-heading mb-4">Profil</h3>
    <div class="card">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama</label>
                    <input value="<?php echo $pecah['nama']; ?>" type="text" value="" class="form-control" name="nama">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input value="<?php echo $pecah['email']; ?>" type="email" class="form-control" name="email">
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <input value="<?php echo $pecah['nohp']; ?>" type="number" class="form-control" name="nohp">
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea value="<?php echo $pecah['alamat']; ?>" class="form-control" name="alamat" id="alamat" rows="10"><?php echo $pecah['alamat']; ?></textarea>
                    <script>
                        CKEDITOR.replace('alamat');
                    </script>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="text" class="form-control" name="password">
                    <input type="hidden" class="form-control" name="passwordlama" value="<?php echo $pecah['password']; ?>">
                    <span class="text-danger">Kosongkan Password jika tidak ingin mengganti</span>
                </div>
                <button class="btn btn-primary float-right pull-right" name="ubah"><i class="glyphicon glyphicon-saved"></i>Simpan</a></button>
            </form>
        </div>
    </div>
</div>
<?php
if (isset($_POST['ubah'])) {
    if ($_POST['password'] == "") {
        $password = $_POST['passwordlama'];
    } else {
        $password = $_POST['password'];
    }
    $conn->query("UPDATE akun SET password='$password',nama='$_POST[nama]', email='$_POST[email]',nohp='$_POST[nohp]', alamat='$_POST[alamat]' WHERE id='$id'") or die(mysqli_error($conn));
    echo "<script>alert('Profil Berhasil Di Ubah');</script>";
    echo "<script>location='profil.php';</script>";
}
include 'footer.php';
?>