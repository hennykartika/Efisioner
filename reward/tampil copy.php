<!DOCTYPE html>
<html>

<head>
    <!-- Basic -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Site Metas -->
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>
        Efisioner
    </title>
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
        }

        @import url(https://fonts.googleapis.com/css?family=Open+Sans:400,600);

        *,
        *:before,
        *:after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #ffff;
            font-family: 'Open Sans', sans-serif;

        }

        table {
            background-color: #f9f9f9;
            background: ffff;
            border-radius: 0.25em;
            border-collapse: collapse;
            margin: 1em auto;
            /* Center the table horizontally */
            max-width: 600px;
            /* Set a maximum width for the table */
        }

        th {
            border-bottom: 1px solid black;
            color: black;
            font-size: 0.75em;
            font-weight: 600;
            padding: 0.5em 1em;
            text-align: center;
        }

        td {
            color: black;
            font-weight: 200;
            padding: 0.35em 1em;
            font-size: 0.75em;
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

        .see-more-container {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
        }

        .see-more-button {
            padding: 10px 20px;
            color: #4169E1;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }

        .content {
            max-width: 600px;
            margin: 0 auto;
            padding-bottom: 60px;
            /* Menambahkan ruang untuk mencegah tumpang tindih */
        }

        td:nth-child(5) {
            font-weight: bold;
        }

        td:nth-child(3) {
            font-size: 0.6em;
        }

        td:nth-child(2) {
            font-size: 0.6em;
        }

        td:nth-child(1) {
            font-size: 0.6em;
        }
    </style>

    <!-- slider stylesheet -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />

    <!-- fonts style -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700|Raleway:400,700&display=swap" rel="stylesheet" />

    <!-- bootstrap core css -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet" />
    <!-- responsive style -->
    <link href="css/responsive.css" rel="stylesheet" />
</head>

<body class="sub_page">
    <div class="hero_area">
        <!-- header section strats -->
        <header class="header_section">
            <div class="container-fluid">
                <nav class="navbar navbar-expand-lg custom_nav-container pt-3">
                    <a class="navbar-brand" href="index.html">
                        <img src="images/logo.png" alt="" />
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <div class="d-flex ml-auto flex-column flex-lg-row align-items-center">
                            <ul class="navbar-nav  ">
                                <li class="nav-item ">
                                    <a class="nav-link" href="index.html">Beranda <span class="sr-only">(current)</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="kuesioner.html">
                                        Kuesioner
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="reward.html">
                                        Reward
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="profil.html">
                                        Profil
                                    </a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link" href="login.html">Login | Daftar</a>
                                </li>
                            </ul>
                            <form class="form-inline my-2 my-lg-0 ml-0 ml-lg-4 mb-3 mb-lg-0">
                                <button class="btn  my-2 my-sm-0 nav_search-btn" type="submit"></button>
                            </form>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
    </div>
    <!-- end  area -->


    <section class="service_section layout_padding-bottom layout_padding2-top">
        <div class="validation-message">
            <h3>Total Reward</h3>
            <p>Rp 21.259,00</p>
        </div>
        <br>

        <div class="navigation">
            <a href="reward_mutasi.php" id="mutasiLink" onclick="highlightNavigation('mutasiLink')">Mutasi</a>
            <a href="reward_klaim.php" id="klaimLink" onclick="highlightNavigation('klaimLink')">Klaim</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Judul
                    <th>Waktu
                    <th>Tanggal
                    <th>Reward
                    <th>Total Saldo
            </thead>
            <tbody>
                <tr>
                    <td>REWARD DARI "Quarter-Life Crisis pada Dewasa Awal di Indonesia"
                    <td>14.40
                    <td>14 September 2030
                    <td><span style="color: green; font-weight: bold;">+50</span></td>
                    <td>Rp21.250
                <tr>
                    <td>REWARD DARI "Pengaruh Motivasi Kerja & Keresahaan Kerja dengan Kepuasaan Kerja sebagai mediasi terhadap Turnover Intention"
                    <td>17.02
                    <td>12 September 2023
                    <td><span style="color: green; font-weight: bold;">+85</span></td>
                    <td>Rp21.200
                <tr class="disabled">
                    <td>KLAIM REWARD KE MANDIRI
                    <td>08.43
                    <td>10 September 2023
                    <td><span style="color: red; font-weight: bold;">-5000</span></td>
                    <td>Rp21.115
                <tr>
                    <td>REWARD DARI “Kebiasaan Masyarakat Dalam Merawat Rambut”
                    <td>13.43
                    <td>7 September 2023
                    <td><span style="color: green; font-weight: bold;">+50</span></td>
                    <td>Rp26.115
                        <div class="see-more-container">
                            <a href="#" class="see-more-button">See More</a>
                        </div>
            </tbody>
        </table>
    </section>
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js">
    </script>
    <script type="text/javascript" src="js/custom.js"></script>
</body>

</html>