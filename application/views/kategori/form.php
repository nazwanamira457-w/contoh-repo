<div class="row"><div class="col-12 col-xl-7">
  <?= form_open(uri_string(), array('class' => 'panel', 'novalidate' => 'novalidate')) ?>
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label" for="nama_kategori">Nama kategori</label>
        <input class="form-control<?= inv('nama_kategori') ?>" id="nama_kategori" name="nama_kategori" value="<?= set_value('nama_kategori', $row ? $row->nama_kategori : '') ?>">
        <?= err('nama_kategori') ?>
      </div>
      <div class="col-12">
        <label class="form-label" for="keterangan">Keterangan</label>
        <textarea class="form-control<?= inv('keterangan') ?>" id="keterangan" name="keterangan" rows="3"><?= set_value('keterangan', $row ? $row->keterangan : '') ?></textarea>
        <?= err('keterangan') ?>
      </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
      <a class="btn btn-outline-secondary" href="<?= site_url('kategori') ?>">Batal</a>
      <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan</button>
    </div>
  <?= form_close() ?>
</div></div>
