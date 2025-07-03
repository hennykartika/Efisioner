<!DOCTYPE html>
<html lang="en">
<?php
include 'config/conn.php';
session_start();
?>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Efisioner</title>
	<link rel="stylesheet" href="assets/backend/node_modules/font-awesome/css/font-awesome.min.css" />
	<link rel="stylesheet" href="assets/backend/node_modules/perfect-scrollbar/dist/css/perfect-scrollbar.min.css" />
	<link rel="stylesheet" href="assets/backend/css/style.css" />
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
	<link href="assets/home/style.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<link rel="shortcut icon" href="foto/logo.png" />
</head>

<body>
	<div class="container-scroller">
		<div class="container-fluid">
			<div class="row">
				<div class="content-wrapper full-page-wrapper d-flex align-items-center auth-pages">
					<div class="card col-lg-8 mx-auto">
						<div class="card-body px-5 py-5">
							<h3 class="card-title text-center mb-3">Daftar</h3>
							<form method="post">
								<div class="form-group">
									<label>Nama</label><br>
									<input type="text" name="nama" value="" class="form-control bg-light border-0" placeholder="Nama" style="height: 55px;">
								</div>
								<div class="form-group">
									<label>Email</label><br>
									<input type="email" name="email" value="" class="form-control bg-light border-0" placeholder="Email" style="height: 55px;">
								</div>
								<div class="form-group">
									<label>No HP</label><br>
									<input type="text" name="nohp" value="" class="form-control bg-light border-0" placeholder="No HP" style="height: 55px;">
								</div>
								<div class="form-group">
									<label>Username</label><br>
									<input type="text" name="username" value="" class="form-control bg-light border-0" placeholder="Username" style="height: 55px;">
								</div>
								<div class="form-group">
									<label>Password</label><br>
									<input type="password" name="password" value="" class="form-control bg-light border-0" placeholder="Password" style="height: 55px;">
								</div>
								<div class="form-group">
									<label>Alamat</label><br>
									<textarea name="alamat" class="form-control bg-light border-0" placeholder="Alamat" style="height: 120px;"></textarea>
								</div>
								<div class="form-group">
									<label>Tanggal Lahir</label><br>
									<input type="date" name="tanggal_lahir" value="" class="form-control bg-light border-0" placeholder="Tanggal Lahir" style="height: 55px;">
								</div>
								<div class="form-group">
									<label>Jenis Kelamin</label><br>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radio1" name="jekel" value="Laki-laki" class="custom-control-input">
										<label class="custom-control-label" for="radio1">Laki-laki</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radio2" name="jekel" value="Perempuan" class="custom-control-input">
										<label class="custom-control-label" for="radio2">Perempuan</label>
									</div>
								</div>
								<div class="form-group">
									<label>Pendidikan</label><br>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radioSD" name="pendidikan" value="SD" class="custom-control-input" required>
										<label class="custom-control-label" for="radioSD">SD</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radioSMP" name="pendidikan" value="SMP" class="custom-control-input" required>
										<label class="custom-control-label" for="radioSMP">SMP</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radioSMA" name="pendidikan" value="SMA" class="custom-control-input" required>
										<label class="custom-control-label" for="radioSMA">SMA</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radio4" name="pendidikan" value="D3" class="custom-control-input" required>
										<label class="custom-control-label" for="radio4">D3</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radio5" name="pendidikan" value="D4" class="custom-control-input" required>
										<label class="custom-control-label" for="radio5">D4</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radio6" name="pendidikan" value="S1" class="custom-control-input" required>
										<label class="custom-control-label" for="radio6">S1</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radio7" name="pendidikan" value="S2" class="custom-control-input" required>
										<label class="custom-control-label" for="radio7">S2</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radio8" name="pendidikan" value="S3" class="custom-control-input" required>
										<label class="custom-control-label" for="radio8">S3</label>
									</div>
								</div>
								<div class="form-group">
									<label>Pekerjaan</label><br>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radioPNS" name="pekerjaan" value="PNS" class="custom-control-input" required>
										<label class="custom-control-label" for="radioPNS">PNS</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radioTNI" name="pekerjaan" value="TNI" class="custom-control-input" required>
										<label class="custom-control-label" for="radioTNI">TNI</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radioPolri" name="pekerjaan" value="Polri" class="custom-control-input" required>
										<label class="custom-control-label" for="radioPolri">Polri</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radioSwasta" name="pekerjaan" value="Swasta" class="custom-control-input" required>
										<label class="custom-control-label" for="radioSwasta">Swasta</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" id="radioWirausaha" name="pekerjaan" value="Wirausaha" class="custom-control-input" required>
										<label class="custom-control-label" for="radioWirausaha">Wirausaha</label>
									</div>
									<div class="custom-control custom-radio custom-control-inline">
										<input type="radio" name="pekerjaan" value="Lainnya" id="lainnya" class="custom-control-input" required>
										<label class="custom-control-label" for="lainnya">Lainnya (sebutkan)</label>
									</div><br>
									<input type="text" id="pekerjaaninput" name="pekerjaaninput" value="" class="form-control bg-light border-0" placeholder="Masukkan Pekerjaan" style="height: 55px; display: none;">
								</div>
								<div class="form-group">
									<label>Instansi</label><br>
									<input type="text" name="instansi" value="" class="form-control bg-light border-0" placeholder="Instansi" style="height: 55px;">
								</div>
								<button class="btn btn-dark w-100 py-3" name="daftar" value="daftar" type="submit">Daftar</button>
								<br>
								<br>
								<center>
									<a class="btn btn-info" href="index.php">Kembali ke Home</a>
									<a class="btn btn-success text-white" href="login.php">Login</a>
								</center>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="assets/home/js/jquery/jquery-2.2.4.min.js"></script>
	<script src="assets/home/js/bootstrap/popper.min.js"></script>
	<script src="assets/home/js/bootstrap/bootstrap.min.js"></script>
	<script src="assets/home/js/plugins/plugins.js"></script>
	<script src="assets/home/js/active.js"></script>
	<script src="assets/backend/node_modules/jquery/dist/jquery.min.js"></script>
	<script src="assets/backend/node_modules/popper.js/dist/umd/popper.min.js"></script>
	<script src="assets/backend/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="assets/backend/node_modules/perfect-scrollbar/dist/js/perfect-scrollbar.jquery.min.js"></script>
	<script src="assets/backend/js/misc.js"></script>
	<script>
		const lainnyaRadio = document.getElementById('lainnya');
		const pekerjaanInput = document.getElementById('pekerjaaninput');

		lainnyaRadio.addEventListener('click', function() {
			pekerjaanInput.style.display = 'block';
		});

		const otherRadios = document.querySelectorAll('input[name="pekerjaan"]:not(#lainnya)');

		for (let radio of otherRadios) {
			radio.addEventListener('click', function() {
				pekerjaanInput.style.display = 'none';
			});
		}
	</script>
</body>

</html>
<?php
if (isset($_POST["daftar"])) {
	$nama = $_POST["nama"];
	$email = $_POST["email"];
	$nohp = $_POST["nohp"];
	$alamat = $_POST["alamat"];
	$username = $_POST["username"];
	$password = $_POST["password"];
	$jekel = $_POST["jekel"];
	$tanggal_lahir = $_POST["tanggal_lahir"];
	$pendidikan = $_POST["pendidikan"];
	$pekerjaan = ($_POST["pekerjaan"] == "Lainnya") ? $_POST["pekerjaaninput"] : $_POST["pekerjaan"];
	$instansi = $_POST["instansi"];
	$ambil = $conn->query("INSERT INTO akun (nama, email, password, nohp, alamat, username, jekel, tanggal_lahir, pendidikan, pekerjaan, instansi)
      VALUES ('$nama', '$email', '$password', '$nohp', '$alamat', '$username', '$jekel', '$tanggal_lahir', '$pendidikan', '$pekerjaan', '$instansi')");
	$akunyangcocok = $conn->affected_rows;
	if ($akunyangcocok >= 1) {
		echo "<script> alert('Daftar Berhasil');</script>";
		echo "<script> location ='index.php';</script>";
	} else {
		echo "<script> alert('Daftar Gagal');</script>";
		echo "<script> location ='login.php';</script>";
	}
}
?>