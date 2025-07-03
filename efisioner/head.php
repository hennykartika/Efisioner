<?php
include('conn.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Efisioner</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="shortcut icon" href="foto/logo.png" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="assets/home/style.css" rel="stylesheet">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>

<body>
    <div class="preloader d-flex align-items-center justify-content-center">
        <div class="lds-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <header class="header-area bg-success">
        <div class="newsbox-main-menu">
            <div class="classy-nav-container breakpoint-off bg-success">
                <div class="container-fluid">
                    <nav class="classy-navbar justify-content-between" id="newsboxNav">
                        <a href="index.html" class="nav-brand"><img src="foto/logo.png" width="40px" alt=""></a>
                        <div class="classy-navbar-toggler">
                            <span class="navbarToggler"><span></span><span></span><span></span></span>
                        </div>
                        <div class="classy-menu">
                            <div class="classycloseIcon">
                                <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
                            </div>
                            <div class="classynav">
                                <ul>
                                    <li><a href="index.php" class="text-warning">Home</a></li>
                                    <li><a href="login.php" class="text-warning">Login Internal</a></li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <section class="breaking-news-area clearfix mt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="breaking-news-ticker d-flex flex-wrap align-items-center">
                        <div id="breakingNewsTicker" class="ticker">
                            <ul>
                                <li><a href="#">Dibuat Oleh : </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>