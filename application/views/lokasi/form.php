<div class="row"><div class="col-12 col-xl-8">
  <?= form_open(uri_string(), array('class' => 'panel', 'novalidate' => 'novalidate')) ?>
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label" for="nama_lokasi">Nama lokasi</label>
        <input class="form-control<?= inv('nama_lokasi') ?>" id="nama_lokasi" name="nama_lokasi" value="<?= set_value('nama_lokasi', $row ? $row->nama_lokasi : '') ?>">
        <?= err('nama_lokasi') ?>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="kota">Kota</label>
        <input class="form-control<?= inv('kota') ?>" id="kota" name="kota" value="<?= set_value('kota', $row ? $row->kota : '') ?>">
        <?= err('kota') ?>
      </div>
      <div class="col-md-8">
        <label class="form-label" for="alamat">Alamat</label>
        <input class="form-control<?= inv('alamat') ?>" id="alamat" name="alamat" value="<?= set_value('alamat', $row ? $row->alamat : '') ?>">
        <?= err('alamat') ?>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="kapasitas">Kapasitas (opsional)</label>
        <input class="form-control<?= inv('kapasitas') ?>" id="kapasitas" name="kapasitas" type="number" min="1" value="<?= set_value('kapasitas', $row ? $row->kapasitas : '') ?>">
        <?= err('kapasitas') ?>
      </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
      <a class="btn btn-outline-secondary" href="<?= site_url('lokasi') ?>">Batal</a>
      <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan</button>
    </div>
  <?= form_close() ?>
</div></div>
