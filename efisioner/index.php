<?php include 'head.php' ?>
<div class="post-details-title-area bg-overlay clearfix" style="background-image: url(foto/coraktni.png)">
	<div class="container-fluid h-100">
		<div class="row h-100 align-items-center">
			<div class="col-12 col-lg-12">
				<div class="post-content">
					<p class="post-title">Efisioner</p>
					<div class="d-flex align-items-center">
						<span class="post-date mr-30">PENGADILAN MILITER I-04 PALEMBANG</span>
					</div>
					<?php
					$ambilrow = $conn->query("SELECT*FROM survey order by idsurvey asc");
					while ($row = $ambilrow->fetch_assoc()) { ?>
						<a href="ikutisurvey.php?id=<?= $row['idsurvey'] ?>" class="btn btn-success py-3 px-5 mt-3 m-1 wow zoomIn" data-wow-delay="0.6s">Jawab <?= $row['namasurvey'] ?></a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include 'foot.php' ?>