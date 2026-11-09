<div class="row"><div class="col-12 col-xl-9">
  <?= form_open(uri_string(), array('class' => 'panel', 'novalidate' => 'novalidate')) ?>
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label" for="nama_event">Nama wisata / event</label>
        <input class="form-control<?= inv('nama_event') ?>" id="nama_event" name="nama_event" value="<?= set_value('nama_event', $row ? $row->nama_event : '') ?>">
        <?= err('nama_event') ?>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="id_kategori">Kategori</label>
        <select class="form-select<?= inv('id_kategori') ?>" id="id_kategori" name="id_kategori">
          <option value="">-- Pilih kategori --</option>
          <?php foreach ($kategori as $k): ?>
            <option value="<?= $k->id_kategori ?>"<?= sel(set_value('id_kategori', $row ? $row->id_kategori : ''), $k->id_kategori) ?>><?= html_escape($k->nama_kategori) ?></option>
          <?php endforeach; ?>
        </select>
        <?= err('id_kategori') ?>
      </div>
      <div class="col-md-6">
        <label class="form-label" for="id_lokasi">Lokasi</label>
        <select class="form-select<?= inv('id_lokasi') ?>" id="id_lokasi" name="id_lokasi">
          <option value="">-- Pilih lokasi --</option>
          <?php foreach ($lokasi as $l): ?>
            <option value="<?= $l->id_lokasi ?>"<?= sel(set_value('id_lokasi', $row ? $row->id_lokasi : ''), $l->id_lokasi) ?>><?= html_escape($l->nama_lokasi.' - '.$l->kota) ?></option>
          <?php endforeach; ?>
        </select>
        <?= err('id_lokasi') ?>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="tanggal_mulai">Tanggal mulai</label>
        <input class="form-control<?= inv('tanggal_mulai') ?>" id="tanggal_mulai" name="tanggal_mulai" type="date" value="<?= set_value('tanggal_mulai', $row ? $row->tanggal_mulai : '') ?>">
        <?= err('tanggal_mulai') ?>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="tanggal_selesai">Tanggal selesai</label>
        <input class="form-control<?= inv('tanggal_selesai') ?>" id="tanggal_selesai" name="tanggal_selesai" type="date" value="<?= set_value('tanggal_selesai', $row ? $row->tanggal_selesai : '') ?>">
        <?= err('tanggal_selesai') ?>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="jam_mulai">Jam mulai (opsional)</label>
        <input class="form-control<?= inv('jam_mulai') ?>" id="jam_mulai" name="jam_mulai" type="time" value="<?= set_value('jam_mulai', ($row && $row->jam_mulai) ? substr($row->jam_mulai, 0, 5) : '') ?>">
        <?= err('jam_mulai') ?>
      </div>
      <div class="col-md-4">
        <label class="form-label" for="status">Status</label>
        <select class="form-select<?= inv('status') ?>" id="status" name="status">
          <?php foreach (array('aktif' => 'Aktif (menerima pesanan)', 'nonaktif' => 'Nonaktif') as $v => $t): ?>
            <option value="<?= $v ?>"<?= sel(set_value('status', $row ? $row->status : 'aktif'), $v) ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
        <?= err('status') ?>
      </div>
      <div class="col-12">
        <label class="form-label" for="deskripsi">Deskripsi (opsional)</label>
        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= set_value('deskripsi', $row ? $row->deskripsi : '') ?></textarea>
      </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
      <a class="btn btn-outline-secondary" href="<?= site_url('event') ?>">Batal</a>
      <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan</button>
    </div>
  <?= form_close() ?>
</div></div>
