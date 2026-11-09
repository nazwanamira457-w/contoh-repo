<?php
$masuk = (bool) $role;
$url_pesan = function ($id) use ($role) {
	if ($role === 'pembeli') { return site_url('loket/pesan/'.$id); }
	if ($role === 'admin')   { return site_url('transaksi/tambah?id_tiket='.$id); }
	return site_url('auth?next=loket/pesan/'.$id);
};
$flash = $this->session->flashdata('sukses');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TiketLokal | Tiket wisata dan event lokal</title>
  <meta name="description" content="Pesan tiket wisata, festival budaya, pertunjukan seni, dan konser lokal. Kuota dihitung langsung, bayar di kasir.">
  <link rel="icon" href="<?= base_url('assets/images/favicon/favicon-32x32.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Figtree:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-icons/bootstrap-icons.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/landing.css') ?>">
</head>
<body class="landing">

<header class="topbar">
  <div class="wrap">
    <a class="brand" href="<?= site_url() ?>"><span class="brand-mark"><i class="bi bi-ticket-perforated-fill"></i></span>TiketLokal</a>
    <nav class="nav-links" aria-label="Navigasi halaman">
      <a href="#event">Event</a>
      <a href="#cara">Cara memesan</a>
    </nav>
    <div class="nav-actions">
      <?php if ($role === 'pembeli'): ?>
        <span class="nav-user"><?= html_escape($nama) ?></span>
        <a class="btn-l is-line is-small" href="<?= site_url('loket') ?>">Loket</a>
        <a class="btn-l is-line is-small" href="<?= site_url('kasir') ?>">Kasir</a>
        <a class="btn-l is-solid is-small" href="<?= site_url('auth/logout') ?>">Keluar</a>
      <?php elseif ($role === 'admin'): ?>
        <span class="nav-user"><?= html_escape($nama) ?></span>
        <a class="btn-l is-line is-small" href="<?= site_url('dashboard') ?>">Dashboard</a>
        <a class="btn-l is-solid is-small" href="<?= site_url('auth/logout') ?>">Keluar</a>
      <?php else: ?>
        <a class="btn-l is-line is-small" href="<?= site_url('auth') ?>">Masuk</a>
        <a class="btn-l is-solid is-small" href="<?= site_url('auth/daftar') ?>">Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<?php if ($flash): ?><div class="flash" role="status"><div class="wrap"><?= html_escape($flash) ?></div></div><?php endif; ?>

<section class="hero">
  <div class="wrap">
    <div>
      <h1>Tiket wisata dan event di kotamu, tanpa antre.</h1>
      <p class="lead">Pilih event, pesan tiketnya, lalu bayar di kasir. Kuota dihitung langsung, jadi tiket yang Anda pesan pasti tersedia.</p>
      <div class="hero-cta">
        <a class="btn-l is-solid" href="#event">Lihat event</a>
        <?php if ($role === 'pembeli'): ?>
          <a class="btn-l is-line" href="<?= site_url('kasir') ?>">Buka kasir</a>
        <?php elseif ($role === 'admin'): ?>
          <a class="btn-l is-line" href="<?= site_url('dashboard') ?>">Ke dashboard</a>
        <?php else: ?>
          <a class="btn-l is-line" href="<?= site_url('auth/daftar') ?>">Buat akun pembeli</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($unggulan): $u = $unggulan; $berlangsung = $u->tanggal_mulai < date('Y-m-d'); $p = tgl_parts($berlangsung ? $u->tanggal_selesai : $u->tanggal_mulai); ?>
    <article class="big-ticket" aria-label="Event unggulan">
      <div class="t-main">
        <div class="t-top"><span class="tag"><?= html_escape($u->nama_kategori) ?></span></div>
        <h2><?= html_escape($u->nama_event) ?></h2>
        <div class="t-meta">
          <span><i class="bi bi-calendar3"></i><?= rentang_tgl($u->tanggal_mulai, $u->tanggal_selesai) ?><?= $u->jam_mulai ? ', pukul '.substr($u->jam_mulai, 0, 5).' WIB' : '' ?></span>
          <span><i class="bi bi-geo-alt"></i><?= html_escape($u->nama_lokasi.', '.$u->kota) ?></span>
        </div>
        <div class="barcode" aria-hidden="true"></div>
        <div class="t-price">
          <div><small>Mulai dari</small><strong><?= $u->harga_min > 0 ? rupiah($u->harga_min) : 'Gratis' ?></strong></div>
          <a class="btn-l is-ink is-small" href="#event-<?= $u->id_event ?>">Lihat tiket</a>
        </div>
      </div>
      <div class="stub">
        <span class="d-label"><?= $berlangsung ? 'Sampai' : 'Mulai' ?></span>
        <span class="d-day"><?= $p[0] ?></span>
        <span class="d-mon"><?= $p[1] ?></span>
        <span class="d-year"><?= $p[2] ?></span>
      </div>
    </article>
    <?php endif; ?>
  </div>
</section>

<main>
  <section class="section" id="event">
    <div class="wrap">
      <div class="section-head">
        <div>
          <h2>Event yang sedang dibuka</h2>
          <p><?= $masuk ? 'Pilih tiket lalu tekan Pesan.' : 'Anda bisa melihat semua tiket di sini. Untuk memesan, masuk atau buat akun terlebih dulu.' ?></p>
        </div>
        <?php if (count($kategori) > 1): ?>
        <div class="chips" role="group" aria-label="Filter kategori">
          <button class="chip" type="button" data-filter="" aria-pressed="true">Semua</button>
          <?php foreach ($kategori as $k): ?><button class="chip" type="button" data-filter="<?= html_escape($k) ?>" aria-pressed="false"><?= html_escape($k) ?></button><?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <?php if ( ! $events): ?>
        <div class="kosong">Belum ada event yang dibuka. Silakan kembali lagi nanti.</div>
      <?php endif; ?>

      <div class="grid">
      <?php foreach ($events as $e):
        $berl = $e->tanggal_mulai < date('Y-m-d');
        $p = tgl_parts($berl ? $e->tanggal_selesai : $e->tanggal_mulai);
        $banyak_hari = $e->tanggal_mulai !== $e->tanggal_selesai; ?>
        <article class="ticket" id="event-<?= $e->id_event ?>" data-kategori="<?= html_escape($e->nama_kategori) ?>">
          <div class="t-body">
            <div class="t-top">
              <span class="tag"><?= html_escape($e->nama_kategori) ?></span>
              <?php if ($berl): ?><span class="tag is-live">Sedang berlangsung</span><?php endif; ?>
            </div>
            <h3><?= html_escape($e->nama_event) ?></h3>
            <?php if ($e->deskripsi): ?><p class="desc"><?= html_escape($e->deskripsi) ?></p><?php endif; ?>
            <div class="t-meta">
              <span><i class="bi bi-calendar3"></i><?= rentang_tgl($e->tanggal_mulai, $e->tanggal_selesai) ?><?= $e->jam_mulai ? ', pukul '.substr($e->jam_mulai, 0, 5).' WIB' : '' ?></span>
              <span><i class="bi bi-geo-alt"></i><?= html_escape($e->nama_lokasi.', '.$e->kota) ?></span>
            </div>
            <ul class="tix">
              <?php foreach ($e->tikets as $t): ?>
                <li>
                  <span class="nm"><?= html_escape($t->nama_tiket) ?>
                    <?php if ($t->sisa <= 0): ?><small>Habis</small>
                    <?php elseif ($t->sisa <= 10): ?><small class="low">Tinggal <?= $t->sisa ?> tiket</small>
                    <?php else: ?><small>Tersedia <?= $t->sisa ?> tiket</small><?php endif; ?></span>
                  <span class="hg"><?= $t->harga > 0 ? rupiah($t->harga) : 'Gratis' ?></span>
                  <?php if ($t->sisa > 0): ?>
                    <a class="btn-l is-ink is-small" href="<?= $url_pesan($t->id_tiket) ?>">Pesan</a>
                  <?php else: ?>
                    <button class="btn-l is-small" disabled>Habis</button>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
              <?php if ( ! $e->tikets): ?><li><span class="nm">Tiket belum tersedia.</span></li><?php endif; ?>
            </ul>
          </div>
          <div class="stub">
            <span class="d-label"><?= $berl ? 'Sampai' : ($banyak_hari ? 'Mulai' : 'Tanggal') ?></span>
            <span class="d-day"><?= $p[0] ?></span>
            <span class="d-mon"><?= $p[1] ?></span>
            <span class="d-year"><?= $p[2] ?></span>
          </div>
        </article>
      <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section steps-band" id="cara">
    <div class="wrap">
      <div class="section-head"><div><h2>Cara memesan</h2><p>Tiga langkah, semuanya bisa dilakukan dari satu akun.</p></div></div>
      <ol class="steps">
        <li><h3>Buat akun atau masuk</h3><p>Daftar dengan nama, email, dan nomor HP. Cukup sekali.</p></li>
        <li><h3>Pesan di Loket</h3><p>Pilih jenis tiket dan jumlahnya. Total harga dihitung otomatis dan kuota langsung terpesan untuk Anda.</p></li>
        <li><h3>Bayar di Kasir</h3><p>Pilih metode pembayaran dan masukkan jumlah yang sesuai. Pesanan Anda berubah menjadi Lunas.</p></li>
      </ol>
    </div>
  </section>
</main>

<footer class="foot">
  <div class="wrap">
    <a class="brand" href="<?= site_url() ?>"><span class="brand-mark"><i class="bi bi-ticket-perforated-fill"></i></span>TiketLokal</a>
    <span>Sistem tiketing wisata dan event lokal.</span>
  </div>
</footer>

<script>
(function () {
  var chips = document.querySelectorAll('.chip');
  var cards = document.querySelectorAll('.ticket[data-kategori]');
  chips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      var f = chip.getAttribute('data-filter');
      chips.forEach(function (c) { c.setAttribute('aria-pressed', c === chip ? 'true' : 'false'); });
      cards.forEach(function (card) { card.hidden = f !== '' && card.getAttribute('data-kategori') !== f; });
    });
  });
})();
</script>
</body>
</html>
