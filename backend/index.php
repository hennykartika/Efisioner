<?php
include 'header.php';
?>
<?php
if (isset($_POST['submit'])) {
	$idsurvey = $_POST['idsurvey'];
	$tahun = $_POST['tahun'];
} else {
	$idsurvey = "";
	$tahun = "";
}
?>
<div class="content-wrapper">
	<div class="row">
		<div class="col-md-12">
			<div class="tab-content tab-transparent-content">
				<div class="tab-pane fade show active" id="business-1" role="tabpanel" aria-labelledby="business-tab">
					<div class="row">
						<div class="col-md-12 grid-margin stretch-card">
							<div class="card">
								<div class="card-body text-center">
									<h5 class="mb-4 text-dark font-weight-bold">
										Data Hasil Kuesioner User
									</h5>
								</div>
							</div>
							<div class="card mt-3">
								<div class="card-body">
									<form method="post">
										<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>Tahun</label>
													<select name="tahun" class="form-control" required>
														<?php
														$nomor = 1;
														$tahunawal = 2017;
														$tahunakhir = date('Y');
														while ($tahunakhir >= $tahunawal) {
														?>
															<option <?php if ($tahun == $tahunakhir) echo 'selected'; ?> value="<?= $tahunakhir ?>"><?= $tahunakhir ?></option>
														<?php
															$tahunakhir = $tahunakhir - 1;
														} ?>
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Seluruh Kuesioner User</label>
													<select name="idsurvey" class="form-control" required>
														<option value="">Pilih Kuesioner</option>
														<?php
														$ambilsurvey = $conn->query("SELECT * FROM survey
														left join akun on survey.id_user = akun.id 
														 order by idsurvey desc");
														while ($listsurvey = $ambilsurvey->fetch_assoc()) {
														?>
															<option <?php if ($idsurvey == $listsurvey['idsurvey']) echo 'selected'; ?> value="<?= $listsurvey['idsurvey'] ?>"><?= $listsurvey['namasurvey'] ?> - <?= $listsurvey['nama'] ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group mb-3">
													<button type="submit" name="submit" value="submit" class="btn btn-primary text-white" style="margin-top:25px">Cari</button>
													<?php
													if ($idsurvey != "") { ?>
														<a target="_blank" href="laporan.php?tahun=<?= $tahun ?>&idsurvey=<?= $idsurvey ?>" class="btn btn-success text-white" style="margin-top:25px">Download Laporan</a>
													<?php } ?>
												</div>
											</div>
										</div>
									</form>
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
include 'footer.php';
?>
<script>
	window.onload = function() {
		<?php $i = 1; ?>
		<?php
		$ambilrow = $conn->query("SELECT*FROM pertanyaan where idsurvey = '$idsurvey' order by idpertanyaan asc");
		while ($data = $ambilrow->fetch_assoc()) {
			$aQuery = "SELECT * FROM listjawaban join jawaban on listjawaban.idjawaban = jawaban.idjawaban WHERE idpertanyaan = '$data[idpertanyaan]' AND jawaban = 'A' and year(waktu) ='$tahun'";
			$aResult = mysqli_query($conn, $aQuery);
			$a = mysqli_num_rows($aResult);

			$bQuery = "SELECT * FROM listjawaban join jawaban on listjawaban.idjawaban = jawaban.idjawaban WHERE idpertanyaan = '$data[idpertanyaan]' AND jawaban = 'B' and year(waktu) ='$tahun'";
			$bResult = mysqli_query($conn, $bQuery);
			$b = mysqli_num_rows($bResult);

			$cQuery = "SELECT * FROM listjawaban join jawaban on listjawaban.idjawaban = jawaban.idjawaban WHERE idpertanyaan = '$data[idpertanyaan]' AND jawaban = 'C' and year(waktu) ='$tahun'";
			$cResult = mysqli_query($conn, $cQuery);
			$c = mysqli_num_rows($cResult);

			$dQuery = "SELECT * FROM listjawaban join jawaban on listjawaban.idjawaban = jawaban.idjawaban WHERE idpertanyaan = '$data[idpertanyaan]' AND jawaban = 'D' and year(waktu) ='$tahun'";
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