<?php if ( ! $events): ?>
  <div class="alert alert-info mt-3">Belum ada event yang membuka penjualan tiket.</div>
<?php endif; ?>
<?php foreach ($events as $e): list($lbl, $cls) = status_waktu_event($e->tanggal_mulai, $e->tanggal_selesai); ?>
  <section class="panel mt-3">
    <div class="panel-header">
      <div>
        <h2 class="h5 mb-1 section-title"><i class="bi bi-calendar-event"></i><span><?= html_escape($e->nama_event) ?></span> <span class="badge <?= $cls ?> ms-1"><?= $lbl ?></span></h2>
        <p class="text-muted mb-0">
          <i class="bi bi-calendar3"></i> <?= rentang_tgl($e->tanggal_mulai, $e->tanggal_selesai) ?><?= $e->jam_mulai ? ', '.substr($e->jam_mulai, 0, 5).' WIB' : '' ?>
          &middot; <i class="bi bi-geo-alt"></i> <?= html_escape($e->nama_lokasi.', '.$e->kota) ?>
          &middot; <?= html_escape($e->nama_kategori) ?>
        </p>
        <?php if ($e->deskripsi): ?><p class="small mb-0 mt-1"><?= html_escape($e->deskripsi) ?></p><?php endif; ?>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Jenis Tiket</th><th class="text-end">Harga</th><th class="text-end">Sisa</th><th class="text-end">Aksi</th></tr></thead>
        <tbody>
        <?php foreach (isset($tikets[$e->id_event]) ? $tikets[$e->id_event] : array() as $t): ?>
          <tr>
            <td class="fw-semibold"><?= html_escape($t->nama_tiket) ?></td>
            <td class="text-end"><?= $t->harga > 0 ? rupiah($t->harga) : 'Gratis' ?></td>
            <td class="text-end"><span class="badge <?= $t->sisa > 0 ? 'text-bg-success' : 'text-bg-danger' ?>"><?= $t->sisa > 0 ? $t->sisa.' tersisa' : 'Habis' ?></span></td>
            <td class="text-end">
              <?php if ($t->sisa > 0): ?>
                <a class="btn btn-primary btn-sm" href="<?= site_url('loket/pesan/'.$t->id_tiket) ?>"><i class="bi bi-cart-plus"></i> Pesan</a>
              <?php else: ?>
                <button class="btn btn-secondary btn-sm" disabled>Habis</button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($tikets[$e->id_event])): ?><tr><td colspan="4" class="text-center text-muted">Belum ada tiket untuk event ini.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
<?php endforeach; ?>
