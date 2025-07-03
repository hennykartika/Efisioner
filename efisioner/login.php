<!DOCTYPE html>
<html lang="en">
<?php
include 'conn.php';
session_start();
?>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Efisioner</title>
	<link rel="stylesheet" href="assets/backend/node_modules/font-awesome/css/font-awesome.min.css" />
	<link rel="stylesheet" href="assets/backend/node_modules/perfect-scrollbar/dist/css/perfect-scrollbar.min.css" />
	<link rel="stylesheet" href="assets/backend/css/style.css" />
	<link rel="shortcut icon" href="foto/logo.png" />
</head>

<body>
	<div class="container-scroller">
		<div class="container-fluid">
			<div class="row">
				<div class="content-wrapper full-page-wrapper d-flex align-items-center auth-pages">
					<div class="card col-lg-4 mx-auto">
						<div class="card-body px-5 py-5">
							<h3 class="card-title text-center mb-3">Login</h3>
							<form method="post">
								<div class="form-group">
									<input type="text" name="username" value="" class="form-control bg-light border-0" placeholder="Username" style="height: 55px;">
								</div>
								<div class="form-group">
									<input type="password" name="password" value="" class="form-control bg-light border-0" placeholder="Password" style="height: 55px;">
								</div>
								<button class="btn btn-dark w-100 py-3" name="login" value="login" type="submit">Login</button>
								<br>
								<br>
								<center>
									<a href="index.php">Kembali ke Home</a>
								</center>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="assets/backend/node_modules/jquery/dist/jquery.min.js"></script>
	<script src="assets/backend/node_modules/popper.js/dist/umd/popper.min.js"></script>
	<script src="assets/backend/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="assets/backend/node_modules/perfect-scrollbar/dist/js/perfect-scrollbar.jquery.min.js"></script>
	<script src="assets/backend/js/misc.js"></script>
</body>

</html>
<?php
if (isset($_POST["login"])) {
	$username = $_POST["username"];
	$password = $_POST["password"];
	$ambil = $conn->query("SELECT * FROM akun
      WHERE username='$username' AND password='$password'");
	$akunyangcocok = $ambil->num_rows;
	if ($akunyangcocok >= 1) {
		$akun = $ambil->fetch_assoc();
		$_SESSION["akun"] = $akun;
		echo "<script> alert('Login Berhasil');</script>";
		echo "<script> location ='backend/index.php';</script>";
	} else {
		echo "<script> alert('Username atau Password anda salah');</script>";
		echo "<script> location ='login.php';</script>";
	}
}
?>