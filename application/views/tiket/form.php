<div class="row"><div class="col-12 col-xl-8">
  <?= form_open(uri_string(), array('class' => 'panel', 'novalidate' => 'novalidate')) ?>
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label" for="id_event">Event</label>
        <select class="form-select<?= inv('id_event') ?>" id="id_event" name="id_event">
          <option value="">-- Pilih event --</option>
          <?php foreach ($events as $e): ?>
            <option value="<?= $e->id_event ?>"<?= sel(set_value('id_event', $row ? $row->id_event : ($pre_event ?: '')), $e->id_event) ?>>
              <?= html_escape($e->nama_event.' ('.rentang_tgl($e->tanggal_mulai, $e->tanggal_selesai).')') ?></option>
          <?php endforeach; ?>
        </select>
        <?= err('id_event') ?>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="nama_tiket">Nama jenis tiket</label>
        <input class="form-control<?= inv('nama_tiket') ?>" id="nama_tiket" name="nama_tiket" placeholder="mis. Reguler, VIP" value="<?= set_value('nama_tiket', $row ? $row->nama_tiket : '') ?>">
        <?= err('nama_tiket') ?>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="harga">Harga (Rp)</label>
        <input class="form-control<?= inv('harga') ?>" id="harga" name="harga" type="number" min="0" step="any" placeholder="0 = gratis" value="<?= set_value('harga', $row ? (float) $row->harga : '') ?>">
        <?= err('harga') ?>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="kuota">Kuota</label>
        <input class="form-control<?= inv('kuota') ?>" id="kuota" name="kuota" type="number" min="1" value="<?= set_value('kuota', $row ? $row->kuota : '') ?>">
        <?= err('kuota') ?>
        <?php if ($row): ?><div class="form-text">Sudah terjual/dipesan: <?= (int) $row->terjual ?> (kuota tidak boleh di bawah ini).</div><?php endif; ?>
      </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
      <a class="btn btn-outline-secondary" href="<?= site_url('tiket') ?>">Batal</a>
      <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan</button>
    </div>
  <?= form_close() ?>
</div></div>
