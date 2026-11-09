<section class="row g-3 mt-1" aria-label="Ringkasan">
  <div class="col-12 col-sm-6 col-xl-3">
    <article class="metric-card metric-primary">
      <div class="metric-top"><span class="metric-label">Event Aktif</span><span class="metric-icon"><i class="bi bi-calendar-event"></i></span></div>
      <div class="metric-value"><?= (int) $ringkasan->event_aktif ?></div>
      <div class="metric-meta"><span>belum selesai &amp; menerima pesanan</span></div>
    </article>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <article class="metric-card metric-success">
      <div class="metric-top"><span class="metric-label">Tiket Terjual</span><span class="metric-icon"><i class="bi bi-ticket-perforated"></i></span></div>
      <div class="metric-value"><?= number_format($ringkasan->tiket_terjual, 0, ',', '.') ?></div>
      <div class="metric-meta"><span class="text-success"><?= number_format($ringkasan->tiket_tersisa, 0, ',', '.') ?></span><span>tiket masih tersisa</span></div>
    </article>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <article class="metric-card metric-warning">
      <div class="metric-top"><span class="metric-label">Pendapatan (Lunas)</span><span class="metric-icon"><i class="bi bi-cash-stack"></i></span></div>
      <div class="metric-value"><?= rupiah($ringkasan->pendapatan) ?></div>
      <div class="metric-meta"><span><?= (int) $ringkasan->trx_total ?> transaksi tercatat</span></div>
    </article>
  </div>
  <div class="col-12 col-sm-6 col-xl-3">
    <article class="metric-card metric-danger">
      <div class="metric-top"><span class="metric-label">Menunggu Pembayaran</span><span class="metric-icon"><i class="bi bi-hourglass-split"></i></span></div>
      <div class="metric-value"><?= (int) $ringkasan->trx_menunggu ?></div>
      <div class="metric-meta"><a href="<?= site_url('transaksi?status=menunggu') ?>">lihat transaksi</a></div>
    </article>
  </div>
</section>

<section class="row g-3 mt-1">
  <div class="col-12 col-xl-5">
    <div class="panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-calendar-check"></i><span>Event Mendatang</span></h2>
          <p class="text-muted mb-0">Ketersediaan tiket per event.</p>
        </div>
        <a class="btn btn-light btn-sm" href="<?= site_url('event') ?>">Semua</a>
      </div>
      <?php if ( ! $mendatang): ?>
        <p class="text-muted mb-0">Belum ada event aktif.</p>
      <?php endif; ?>
      <?php foreach ($mendatang as $e):
        $pct = $e->kuota > 0 ? round($e->terjual / $e->kuota * 100) : 0; ?>
        <div class="mb-3">
          <div class="d-flex justify-content-between gap-2">
            <a class="fw-semibold" href="<?= site_url('event/detail/'.$e->id_event) ?>"><?= html_escape($e->nama_event) ?></a>
            <small class="text-muted text-nowrap"><?= $e->terjual ?>/<?= $e->kuota ?></small>
          </div>
          <small class="text-muted"><?= rentang_tgl($e->tanggal_mulai, $e->tanggal_selesai) ?> &middot; <?= html_escape($e->nama_lokasi) ?></small>
          <div class="progress stock-bar mt-1" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar <?= $pct >= 90 ? 'bg-danger' : ($pct >= 60 ? 'bg-warning' : 'bg-success') ?>" style="width: <?= $pct ?>%"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="col-12 col-xl-7">
    <div class="panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-receipt"></i><span>Transaksi Terbaru</span></h2>
          <p class="text-muted mb-0">Pemesanan tiket terakhir.</p>
        </div>
        <a class="btn btn-light btn-sm" href="<?= site_url('transaksi') ?>">Semua</a>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead><tr><th>Kode</th><th>Pemesan</th><th>Tiket</th><th class="text-end">Total</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($terbaru as $t): ?>
            <tr>
              <td class="text-nowrap"><a href="<?= site_url('transaksi/detail/'.$t->id_transaksi) ?>"><?= html_escape($t->kode_transaksi) ?></a></td>
              <td><?= html_escape($t->nama_pemesan) ?></td>
              <td><?= html_escape($t->nama_event) ?><br><small class="text-muted"><?= html_escape($t->nama_tiket) ?> &times; <?= $t->jumlah ?></small></td>
              <td class="text-end"><?= rupiah($t->total_harga) ?></td>
              <td><?= badge_status($t->status) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if ( ! $terbaru): ?><tr><td colspan="5" class="text-center text-muted">Belum ada transaksi.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
