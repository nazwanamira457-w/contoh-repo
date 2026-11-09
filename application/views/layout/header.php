<?php
if ($user_role === 'pembeli')
{
	$menu = array(
		array('',       'Beranda', 'bi-house',     '__beranda'),
		array('loket',  'Loket',  'bi-shop',      'loket'),
		array('kasir',  'Kasir',  'bi-cash-coin', 'kasir'),
	);
	$beranda = 'loket';
	$label_role = 'Pembeli';
}
else
{
	$menu = array(
		array('dashboard', 'Dashboard',      'bi-speedometer2',      'dashboard'),
		array('event',     'Wisata / Event', 'bi-calendar-event',    'event'),
		array('tiket',     'Jenis Tiket',    'bi-ticket-perforated', 'tiket'),
		array('transaksi', 'Transaksi',      'bi-receipt',           'transaksi'),
		array('pemesan',   'Pemesan',        'bi-people',            'pemesan'),
		array('laporan',   'Rekap Tiket',    'bi-bar-chart-line',    'laporan'),
		array('kategori',  'Kategori',       'bi-tags',              'kategori'),
		array('lokasi',    'Lokasi',         'bi-geo-alt',           'lokasi'),
		array('',          'Lihat Situs',    'bi-house',             '__beranda'),
	);
	$beranda = 'dashboard';
	$label_role = 'Pengelola';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= html_escape($title) ?> | TiketLokal</title>
  <link rel="icon" href="<?= base_url('assets/images/favicon/favicon-32x32.png') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-icons/bootstrap-icons.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/tiketing.css') ?>">
</head>
<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    <aside class="admin-sidebar" id="adminSidebar" aria-label="Navigasi utama">
      <div class="sidebar-header">
        <a class="brand-mark" href="<?= site_url($beranda) ?>" aria-label="TiketLokal">
          <span class="brand-icon"><i class="bi bi-ticket-perforated-fill" aria-hidden="true"></i></span>
          <span class="brand-copy">
            <span class="brand-title">TiketLokal</span>
            <span class="brand-subtitle">Tiketing Wisata &amp; Event</span>
          </span>
        </a>
      </div>

      <nav class="sidebar-nav">
        <?php foreach ($menu as $m): ?>
          <a class="nav-link<?= $menu_aktif === $m[3] ? ' active' : '' ?>" href="<?= site_url($m[0]) ?>"<?= $menu_aktif === $m[3] ? ' aria-current="page"' : '' ?>>
            <span class="nav-icon"><i class="bi <?= $m[2] ?>" aria-hidden="true"></i></span>
            <span class="nav-text"><?= $m[1] ?></span>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="sidebar-user">
        <img class="avatar-img avatar-md sidebar-user-avatar" src="<?= base_url('assets/images/avatar/avatar.jpg') ?>" alt="">
        <strong><?= html_escape($user_nama) ?></strong>
        <small><?= $label_role ?></small>
      </div>

      <div class="sidebar-footer">
        <span class="status-dot"></span>
        <span class="sidebar-footer-text">Sistem berjalan normal</span>
      </div>
    </aside>

    <div class="admin-main">
      <nav class="navbar admin-navbar navbar-expand bg-white">
        <div class="container-fluid px-3 px-lg-4">
          <button class="sidebar-toggle" type="button" data-sidebar-toggle aria-controls="adminSidebar" aria-expanded="true" aria-label="Buka/tutup sidebar">
            <span></span><span></span><span></span>
          </button>

          <?php if ($user_role === 'admin'): ?>
          <form class="d-none d-md-flex ms-3 flex-grow-1" role="search" method="get" action="<?= site_url('transaksi') ?>">
            <input class="form-control search-input" type="search" name="q" placeholder="Cari transaksi (kode, pemesan, event)" aria-label="Cari transaksi">
          </form>
          <?php endif; ?>

          <div class="navbar-actions ms-auto">
            <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="Ganti tema" title="Ganti tema">
              <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
            </button>
            <div class="dropdown">
              <button class="profile-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="avatar-img avatar-sm" src="<?= base_url('assets/images/avatar/avatar.jpg') ?>" alt="">
                <span class="profile-name d-none d-sm-inline"><?= html_escape($user_nama) ?></span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= site_url('auth/logout') ?>"><i class="bi bi-box-arrow-right"></i> Keluar</a></li>
              </ul>
            </div>
          </div>
        </div>
      </nav>

      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          <div class="page-heading">
            <div class="page-heading-copy">
              <span class="page-icon"><i class="bi <?= $icon ?>" aria-hidden="true"></i></span>
              <div>
                <p class="eyebrow mb-1"><?= html_escape($eyebrow) ?></p>
                <h1 class="h3 mb-1"><?= html_escape($title) ?></h1>
                <p class="text-muted mb-0"><?= html_escape($subtitle) ?></p>
              </div>
            </div>
            <div class="heading-actions"><?= $actions ?></div>
          </div>

          <?php if ($this->session->flashdata('sukses')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
              <i class="bi bi-check-circle"></i> <?= html_escape($this->session->flashdata('sukses')) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
          <?php endif; ?>
          <?php $gagal = $this->session->flashdata('gagal') ?: $pesan_langsung; if ($gagal): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
              <i class="bi bi-exclamation-octagon"></i> <?= html_escape($gagal) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
          <?php endif; ?>
