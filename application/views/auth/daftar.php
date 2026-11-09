<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar | TiketLokal</title>
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
      <a class="auth-brand" href="<?= site_url() ?>"><span class="brand-icon"><i class="bi bi-ticket-perforated-fill" aria-hidden="true"></i></span><span><strong>TiketLokal</strong><small>Daftar akun pembeli</small></span></a>

      <?= form_open('auth/daftar', array('novalidate' => 'novalidate')) ?>
        <div class="mb-3 mt-3">
          <h1 class="h3 mb-1">Daftar</h1>
          <p class="text-muted mb-0">Buat akun untuk memesan dan membayar tiket.</p>
        </div>
        <?php if ( ! empty($pesan)): ?><div class="alert alert-danger py-2"><?= html_escape($pesan) ?></div><?php endif; ?>

        <div class="mb-3"><label class="form-label" for="nama">Nama lengkap</label>
          <input class="form-control<?= inv('nama') ?>" id="nama" name="nama" value="<?= set_value('nama') ?>"><?= err('nama') ?></div>
        <div class="row g-3 mb-3">
          <div class="col-md-6"><label class="form-label" for="email">Email</label>
            <input class="form-control<?= inv('email') ?>" id="email" name="email" type="email" value="<?= set_value('email') ?>"><?= err('email') ?></div>
          <div class="col-md-6"><label class="form-label" for="no_hp">No. HP</label>
            <input class="form-control<?= inv('no_hp') ?>" id="no_hp" name="no_hp" inputmode="tel" placeholder="08xxxxxxxxxx" value="<?= set_value('no_hp') ?>"><?= err('no_hp') ?></div>
        </div>
        <div class="mb-3"><label class="form-label" for="username">Username</label>
          <input class="form-control<?= inv('username') ?>" id="username" name="username" autocomplete="username" value="<?= set_value('username') ?>"><?= err('username') ?></div>
        <div class="row g-3 mb-4">
          <div class="col-md-6"><label class="form-label" for="password">Password</label>
            <input class="form-control<?= inv('password') ?>" id="password" name="password" type="password" autocomplete="new-password"><?= err('password') ?></div>
          <div class="col-md-6"><label class="form-label" for="password2">Ulangi password</label>
            <input class="form-control<?= inv('password2') ?>" id="password2" name="password2" type="password" autocomplete="new-password"><?= err('password2') ?></div>
        </div>
        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-person-plus"></i> Buat Akun</button>
      <?= form_close() ?>
      <div class="text-center small mt-3"><a href="<?= site_url() ?>"><i class="bi bi-arrow-left"></i> Kembali ke beranda</a></div>
      <div class="auth-footer">Sudah punya akun? <a href="<?= site_url('auth') ?>">Masuk</a></div>
    </section>
  </main>
  <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/main.js') ?>"></script>
</body>
</html>
