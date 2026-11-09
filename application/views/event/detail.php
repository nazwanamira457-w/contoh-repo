<?php list($lbl, $cls) = status_waktu_event($ev->tanggal_mulai, $ev->tanggal_selesai); ?>
<section class="row g-3 mt-1">
  <div class="col-12 col-xl-5">
    <div class="panel h-100">
      <div class="panel-header">
        <h2 class="h5 mb-1 section-title"><i class="bi bi-info-circle"></i><span>Informasi Event</span></h2>
        <a class="btn btn-light btn-sm" href="<?= site_url('event/ubah/'.$ev->id_event) ?>"><i class="bi bi-pencil"></i> Ubah</a>
      </div>
      <dl class="row struk mb-0">
        <dt class="col-sm-4">Kategori</dt><dd class="col-sm-8"><?= html_escape($ev->nama_kategori) ?></dd>
        <dt class="col-sm-4">Tanggal</dt><dd class="col-sm-8"><?= rentang_tgl($ev->tanggal_mulai, $ev->tanggal_selesai) ?><?= $ev->jam_mulai ? ', '.substr($ev->jam_mulai, 0, 5).' WIB' : '' ?></dd>
        <dt class="col-sm-4">Lokasi</dt><dd class="col-sm-8"><?= html_escape($ev->nama_lokasi) ?><br><small class="text-muted"><?= html_escape(trim($ev->alamat.', '.$ev->kota, ', ')) ?></small></dd>
        <dt class="col-sm-4">Waktu</dt><dd class="col-sm-8"><span class="badge <?= $cls ?>"><?= $lbl ?></span></dd>
        <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><?= badge_status($ev->status) ?></dd>
        <dt class="col-sm-4">Deskripsi</dt><dd class="col-sm-8"><?= nl2br(html_escape($ev->deskripsi)) ?: '-' ?></dd>
      </dl>
    </div>
  </div>
  <div class="col-12 col-xl-7">
    <div class="panel h-100">
      <div class="panel-header">
        <h2 class="h5 mb-1 section-title"><i class="bi bi-ticket-perforated"></i><span>Jenis Tiket</span></h2>
        <a class="btn btn-primary btn-sm" href="<?= site_url('tiket/tambah?id_event='.$ev->id_event) ?>"><i class="bi bi-plus-lg"></i> Tambah</a>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead><tr><th>Tiket</th><th class="text-end">Harga</th><th class="text-end">Kuota</th><th class="text-end">Terjual</th><th class="text-end">Sisa</th><th class="text-end">Aksi</th></tr></thead>
          <tbody>
          <?php foreach ($tikets as $t): ?>
            <tr>
              <td class="fw-semibold"><?= html_escape($t->nama_tiket) ?></td>
              <td class="text-end"><?= rupiah($t->harga) ?></td>
              <td class="text-end"><?= $t->kuota ?></td>
              <td class="text-end"><?= $t->terjual ?></td>
              <td class="text-end"><span class="badge <?= $t->sisa > 0 ? 'text-bg-success' : 'text-bg-danger' ?>"><?= $t->sisa ?></span></td>
              <td class="text-end table-actions text-nowrap">
                <a class="btn btn-light btn-sm" href="<?= site_url('tiket/ubah/'.$t->id_tiket) ?>"><i class="bi bi-pencil"></i></a>
                <?= form_open('tiket/hapus/'.$t->id_tiket, array('onsubmit' => "return confirm('Hapus jenis tiket ini?')")) ?>
                  <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i></button>
                <?= form_close() ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if ( ! $tikets): ?><tr><td colspan="6" class="text-center text-muted">Belum ada jenis tiket untuk event ini.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
