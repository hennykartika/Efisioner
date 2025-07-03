<?php include('../layout/header.php');
if (!isset($_SESSION['akun'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!')</script>";
    echo "<script>location='../login.php';</script>";
    exit();
}
$id_user = $_SESSION['akun']['id'];
$data = $conn->query("SELECT * from akun WHERE id = '$id_user'");
$data = $data->fetch_assoc();

$data_history = $conn->query("SELECT * 
FROM history_point
LEFT JOIN survey ON history_point.idsurvey = survey.idsurvey
WHERE history_point.id_user = '$id_user'
ORDER BY history_point.idsurvey ASC");
?>

<style>
    .validation-message {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-color: #0077B6;
        padding: 20px;
        margin-top: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        color: white;
    }

    @import url(https://fonts.googleapis.com/css?family=Open+Sans:400,600);

    *,
    *:before,
    *:after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    table {
        background-color: #f9f9f9;
        background: ffff;
        border-radius: 0.25em;
        border-collapse: collapse;
        margin: 1em auto;
        max-width: 1200px;
    }

    th {
        border-bottom: 1px solid black;
        color: black;
        font-size: 1em;
        font-weight: 600;
        padding: 0.5em 1em;
        text-align: center;
    }

    td {
        color: black;
        font-weight: 200;
        padding: 0.35em 1em;
        font-size: 0.75em;
        text-align: center;
    }

    .disabled td {
        color: black;
    }

    tbody tr {
        transition: background 0.25s ease;
    }

    tbody tr:hover {
        background: #AFEEEE;
    }

    /* Set a maximum width for the table container */
    .table-container {
        max-width: 400px;
        margin: 0 auto;
    }

    /* Mengatur navigasi */
    .navigation {
        text-align: center;
        margin-bottom: 20px;
        margin-right: 280px;
    }

    .navigation a {
        margin-left: 280px;

    }

    td {
        font-size: 13px;
    }
</style>
<!-- Content Here -->
<section class="agency_section">
    <div class="validation-message">
        <p>Total Reward</p>
        <p><span style="font-size: 40px;">Rp <?= number_format($data['reward'], 0, ',', '.') ?></span></p>
    </div>
    <br>
    <table>
        <thead>
            <tr>
                <th>JUDUL</th>
                <th width="20%">WAKTU</th>
                <th width="15%">REWARD</th>
        </thead>
        <tbody>
            <?php
            while ($row = $data_history->fetch_assoc()) { ?>
                <tr>
                    <td><?= ($row["idsurvey"] != "") ? $row['namasurvey'] : $row["deskripsi"] ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <?= ($row["jumlah"] > 0) ? "<span style='color: green; font-weight: bold;'>+" . $row['jumlah'] . "</span>"
                            : "<span style='color: red; font-weight: bold;'>" . $row['jumlah'] . "</span>" ?>

                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>
<section class="service_section layout_padding-bottom layout_padding2-top">

</section>

<?php include('../layout/footer.php'); ?>