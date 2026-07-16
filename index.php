<?php
include 'functions.php';
if (empty($_SESSION['login']))
	header("location:login.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<title>Double Exponential Smoothing PHP</title>
	<link href="assets/css/styles.css?v=1.4" rel="stylesheet" />
	<link href="assets/fontawesome/css/all.min.css" rel="stylesheet" />
	<script src="assets/js/highcharts.js"></script>
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<script src="assets/js/jquery-3.7.1.min.js"></script>
	<script src="assets/js/scripts.js"></script>
</head>

<body class="sb-nav-fixed">
	<nav class="sb-topnav navbar navbar-expand navbar-dark">
		<!-- Navbar Brand-->
		<img class="navbar-logo ps-3" src="LOGO_RAMEN.png" alt="">
		<!-- Sidebar Toggle-->
		<button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
		<!-- Navbar-->
		<ul class="navbar-nav ms-auto me-3 me-lg-4">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i> <?= _session('login') ?></a>
				<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
					<li><a class="dropdown-item" href="?m=password">Password</a></li>
					<li><a class="dropdown-item" href="aksi.php?act=logout">Logout</a></li>
				</ul>
			</li>
		</ul>
	</nav>
	<div id="layoutSidenav">
		<div id="layoutSidenav_nav">
			<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
				<div class="sb-sidenav-menu">
					<div class="nav">
						<a class="nav-link" href="?m=home">
							<div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
							Beranda
						</a>
						<a class="nav-link" href="?m=produk" <?= is_hidden('produk') ?>>
							<div class="sb-nav-link-icon"><i class="fas fa-th"></i></div>
							Produk
						</a>
						<a class="nav-link" href="?m=jual" <?= is_hidden('jual') ?>>
							<div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
							Penjualan
						</a>
						<a class="nav-link" href="?m=produk_keluar" <?= is_hidden('produk_keluar') ?>>
							<div class="sb-nav-link-icon"><i class="fas fa-tags"></i></div>
							Laporan Penjualan
						</a>
						<a class="nav-link" href="?m=alpha" <?= is_hidden('alpha') ?>>
							<div class="sb-nav-link-icon"><i class="fas fa-ranking-star"></i></div>
							Alpha Terbaik
						</a>
						<a class="nav-link" href="?m=des" <?= is_hidden('des') ?>>
							<div class="sb-nav-link-icon"><i class="fas fa-signal"></i></div>
							Peramalan
						</a>
						<a class="nav-link" href="?m=hasil" <?= is_hidden('hasil') ?>>
							<div class="sb-nav-link-icon"><i class="fas fa-calendar"></i></div>
							Laporan Peramalan
						</a>
						<a class="nav-link" href="?m=user" <?= is_hidden('user') ?>>
							<div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
							User
						</a>
					</div>
				</div>
			</nav>
		</div>
		<div id="layoutSidenav_content">
			<main>
				<div class="container-fluid px-4 py-3">
					<?php
					if (!_session('login') && !in_array($mod, array('', 'home', 'hitung', 'login', 'tentang')))
						$mod = 'login';

					if (file_exists($mod . '.php'))
						include $mod . '.php';
					else
						include 'home.php';
					?>
				</div>
			</main>
			<footer class="py-4 bg-light mt-auto">
				<div class="container-fluid px-4">
					<div class="d-flex align-items-center justify-content-between small">
						<div class="text-muted">Copyright &copy; Ramen Kaizenka <?= date('Y') ?></div>
					</div>
				</div>
			</footer>
		</div>
	</div>
</body>

</html>