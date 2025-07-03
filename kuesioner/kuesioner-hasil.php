<?php
include '../layout/header.php';
?>
<?php
if (isset($_POST['submit'])) {
	$idsurvey = $_POST['idsurvey'];
	$tahun = $_POST['tahun'];
} else {
	$idsurvey = $_GET['id'];
	$tahun = "";
}
?>
<style>
	.content {
		text-align: center;
		margin: 0 auto;
		width: 97%;
	}
</style>
<div class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="tab-content tab-transparent-content">
				<div class="tab-pane fade show active" id="business-1" role="tabpanel" aria-labelledby="business-tab">
					<div class="row">
						<div class="col-md-12 grid-margin stretch-card">
							<div class="card mb-3">
								<div class="card-body text-center">
									<h5 class="mb-1 text-dark font-weight-bold">
										Data Kuesioner
									</h5>
									<?php
									if ($idsurvey != "") { ?>
										<a target="_blank" href="laporan.php?idsurvey=<?= $idsurvey ?>" class="btn btn-success text-white" style="margin-top:25px">Download Laporan</a>
									<?php } ?>
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
															<a href="kuesioner-hasil-jawaban.php?id=<?php echo $data['idjawaban']; ?>&idsurvey=<?php echo $_GET['id']; ?>" class="btn btn-success btn-sm m-1">Jawaban</a>
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
							<div class="card mt-3">
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered table-striped" id="tabel">
											<thead>
												<tr>
													<th>Grafik</th>
												</tr>
											</thead>
											<tbody>
												<?php $nomor = 1;
												$ambilrow = $conn->query("SELECT*FROM pertanyaan where idsurvey = '$idsurvey' order by idpertanyaan asc");
												while ($data = $ambilrow->fetch_assoc()) { ?>
													<tr>
														<td>
															<div id="chartContainer<?= $nomor ?>" style="height: 370px; width: 100%;"></div>
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
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
include '../layout/footer.php';
?>
<script>
	$(document).ready(function() {
		$('#tabel').DataTable();
	});
	window.onload = function() {
		<?php $i = 1; ?>
		<?php
		$ambilrow = $conn->query("SELECT*FROM pertanyaan where idsurvey = '$idsurvey' order by idpertanyaan asc");
		while ($data = $ambilrow->fetch_assoc()) {
			$aQuery = "SELECT * FROM listjawaban join jawaban on listjawaban.idjawaban = jawaban.idjawaban WHERE idpertanyaan = '$data[idpertanyaan]' AND jawaban = 'A'";
			$aResult = mysqli_query($conn, $aQuery);
			$a = mysqli_num_rows($aResult);

			$bQuery = "SELECT * FROM listjawaban join jawaban on listjawaban.idjawaban = jawaban.idjawaban WHERE idpertanyaan = '$data[idpertanyaan]' AND jawaban = 'B'";
			$bResult = mysqli_query($conn, $bQuery);
			$b = mysqli_num_rows($bResult);

			$cQuery = "SELECT * FROM listjawaban join jawaban on listjawaban.idjawaban = jawaban.idjawaban WHERE idpertanyaan = '$data[idpertanyaan]' AND jawaban = 'C'";
			$cResult = mysqli_query($conn, $cQuery);
			$c = mysqli_num_rows($cResult);

			$dQuery = "SELECT * FROM listjawaban join jawaban on listjawaban.idjawaban = jawaban.idjawaban WHERE idpertanyaan = '$data[idpertanyaan]' AND jawaban = 'D'";
			$dResult = mysqli_query($conn, $dQuery);
			$d = mysqli_num_rows($dResult);

			$e = $a + $b + $c + $d;
		?>
			var chart<?= $i ?> = new CanvasJS.Chart("chartContainer<?= $i ?>", {
				theme: "light2",
				animationEnabled: true,
				title: {
					text: "<?= $data['pertanyaan'] ?>"
				},
				data: [{
					type: "pie",
					indexLabel: "{symbol} - {y}",
					yValueFormatString: "#,##0.0\"%\"",
					showInLegend: true,
					legendText: "{label} : {y}",
					dataPoints: [{
							label: "<?= $data['a'] ?>",
							symbol: "<?= $data['a'] ?>",
							y: <?php
								if ($a == "0") {
									echo "0";
								} else {
									echo ($a / $e) * 100;
								}
								?>
						},
						{
							label: "<?= $data['b'] ?>",
							symbol: "<?= $data['b'] ?>",
							y: <?php
								if ($b == "0") {
									echo "0";
								} else {
									echo ($b / $e) * 100;
								}
								?>
						},
						{
							label: "<?= $data['c'] ?>",
							symbol: "<?= $data['c'] ?>",
							y: <?php
								if ($c == "0") {
									echo "0";
								} else {
									echo ($c / $e) * 100;
								}
								?>
						},
						{
							label: "<?= $data['d'] ?>",
							symbol: "<?= $data['d'] ?>",
							y: <?php
								if ($d == "0") {
									echo "0";
								} else {
									echo ($d / $e) * 100;
								}
								?>
						}
					]
				}]
			});
			chart<?= $i ?>.render();
			<?php $i++; ?>
		<?php } ?>
	}
</script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>