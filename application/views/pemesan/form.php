<div class="row"><div class="col-12 col-xl-8">
  <?= form_open(uri_string(), array('class' => 'panel', 'novalidate' => 'novalidate')) ?>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label" for="nama">Nama lengkap</label>
        <input class="form-control<?= inv('nama') ?>" id="nama" name="nama" value="<?= set_value('nama', $row ? $row->nama : '') ?>">
        <?= err('nama') ?>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="email">Email</label>
        <input class="form-control<?= inv('email') ?>" id="email" name="email" type="email" value="<?= set_value('email', $row ? $row->email : '') ?>">
        <?= err('email') ?>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="no_hp">No. HP</label>
        <input class="form-control<?= inv('no_hp') ?>" id="no_hp" name="no_hp" inputmode="tel" placeholder="08xxxxxxxxxx" value="<?= set_value('no_hp', $row ? $row->no_hp : '') ?>">
        <?= err('no_hp') ?>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="alamat">Alamat (opsional)</label>
        <input class="form-control<?= inv('alamat') ?>" id="alamat" name="alamat" value="<?= set_value('alamat', $row ? $row->alamat : '') ?>">
        <?= err('alamat') ?>
      </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
      <a class="btn btn-outline-secondary" href="<?= site_url('pemesan') ?>">Batal</a>
      <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan</button>
    </div>
  <?= form_close() ?>
</div></div>
