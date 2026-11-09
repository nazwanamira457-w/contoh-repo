<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk | TiketLokal</title>
  <link rel="icon" href="<?= base_url('assets/images/favicon/favicon-32x32.png') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/vendors/bootstrap-icons/bootstrap-icons.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="auth-body">
  <button class="icon-button theme-toggle auth-theme-toggle" type="button" data-theme-toggle aria-label="Ganti tema" title="Ganti tema">
    <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
  </button>
  <main class="auth-page">
    <section class="auth-card">
      <a class="auth-brand" href="<?= site_url() ?>"><span class="brand-icon"><i class="bi bi-ticket-perforated-fill" aria-hidden="true"></i></span><span><strong>TiketLokal</strong><small>Sistem Tiketing Wisata &amp; Event Lokal</small></span></a>
      <div class="auth-visual"><img src="<?= base_url('assets/images/png/dasher-ui-bootstrap-5.jpg') ?>" alt="Tampilan aplikasi"></div>

      <?= form_open('auth', array('novalidate' => 'novalidate')) ?>
        <div class="mb-4">
          <p class="eyebrow mb-1">Akses Pengelola</p>
          <h1 class="h3 mb-1">Masuk</h1>
          <p class="text-muted mb-0">Masuk untuk mengelola event dan tiket.</p>
        </div>

        <?php if ($this->session->flashdata('gagal')): ?>
          <div class="alert alert-danger py-2"><?= html_escape($this->session->flashdata('gagal')) ?></div>
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label" for="username">Username</label>
          <input class="form-control<?= inv('username') ?>" id="username" name="username" type="text" value="<?= set_value('username') ?>" autofocus>
          <?= err('username') ?>
        </div>
        <div class="mb-4">
          <label class="form-label" for="password">Password</label>
          <input class="form-control<?= inv('password') ?>" id="password" name="password" type="password">
          <?= err('password') ?>
        </div>
        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Masuk</button>
      <?= form_close() ?>
      <div class="text-center small mt-3"><a href="<?= site_url() ?>"><i class="bi bi-arrow-left"></i> Kembali ke beranda</a></div>
      <div class="auth-footer">Belum punya akun? <a href="<?= site_url('auth/daftar') ?>">Daftar sebagai pembeli</a></div>
      <div class="text-muted small text-center mt-2">Demo &mdash; Admin: <strong>admin</strong>/<strong>admin123</strong> &middot; Pembeli: <strong>pembeli</strong>/<strong>pembeli123</strong></div>
    </section>
  </main>
  <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
