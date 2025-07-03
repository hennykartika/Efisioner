<?php

$currentUrl = $_SERVER['REQUEST_URI'];

// Menghapus query string dari URL
$cleanUrl = explode('?', $currentUrl)[0];

// Mendapatkan segment URI
$segments = explode('/', $cleanUrl);

// Menghapus segment kosong dan mendapatkan segment ke-2 atau ke-1 jika tidak ada segment ke-2
$uri = isset($segments[2]) ? $segments[2] : $segments[1];
$uri = str_replace('.php', '', $uri);
// echo $_SESSION['akun']['level'];
$level = $level = isset($_SESSION['akun']['level']) ? $_SESSION['akun']['level'] : null;
?>

<nav class="navbar navbar-expand-lg custom_nav-container pt-3">
    <a class="navbar-brand" href="index">
        <img src="<?= base_url(); ?>assets/images/logo.png" alt="" />
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <div class="d-flex ml-auto flex-column flex-lg-row align-items-center">
            <ul class="navbar-nav  ">
                <?php if ($level == "Admin") { ?>
                    <li class="nav-item active">
                        <a class="nav-link bg-info" href="<?= base_url('backend/index'); ?>">Menu Admin</a>
                    </li>
                <?php } ?>
                <li class="nav-item <?= ($uri == 'index' || $uri == '') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('index'); ?>">Beranda</a>
                </li>
                <li class="nav-item <?= ($uri == 'kuesioner' || $uri == 'kuesioner-ajukan') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('kuesioner/tampil'); ?>">
                        Kuesioner
                    </a>
                </li>
                <li class="nav-item <?= ($uri == 'reward') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('reward/tampil'); ?>">
                        Reward
                    </a>
                </li>
                <li class="nav-item <?= ($uri == 'profil' || $uri == 'profil-setting') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('profil/tampil'); ?>">
                        Profil
                    </a>
                </li>
                <li class="nav-item active">
                    <?php if (isset($_SESSION['logged_in'])) { ?>
                        <a class="nav-link bg-danger" href="<?= base_url('logout'); ?>">Logout</a>
                    <?php } else { ?>
                        <a class="nav-link" href="<?= base_url('login'); ?>">Login | Daftar</a>
                    <?php } ?>
                </li>
            </ul>
            <!-- <form class="form-inline my-2 my-lg-0 ml-0 ml-lg-4 mb-3 mb-lg-0">
                <button class="btn  my-2 my-sm-0 nav_search-btn" type="submit"></button>
            </form> -->
        </div>
    </div>
</nav>


<script>
    let uri = "<?= $uri ?>";

    function base_url(url) {
        var res = "<?= base_url(); ?>" + url;
        return res;
    }
</script>