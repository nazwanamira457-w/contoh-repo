<div class="row"><div class="col-12 col-xl-8">
  <section class="panel struk">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
      <div>
        <p class="text-muted small mb-0">Kode transaksi</p>
        <h2 class="h4 mb-0"><?= html_escape($trx->kode_transaksi) ?></h2>
      </div>
      <div class="fs-6"><?= badge_status($trx->status) ?></div>
    </div>

    <div class="row g-4">
      <div class="col-md-6">
        <h3 class="h6 text-uppercase text-muted">Pemesan</h3>
        <p class="mb-0 fw-semibold"><?= html_escape($trx->nama_pemesan) ?></p>
        <p class="mb-0"><?= html_escape($trx->email) ?></p>
        <p class="mb-0"><?= html_escape($trx->no_hp) ?></p>
        <?php if ($trx->alamat): ?><p class="mb-0 text-muted"><?= html_escape($trx->alamat) ?></p><?php endif; ?>
      </div>
      <div class="col-md-6">
        <h3 class="h6 text-uppercase text-muted">Event</h3>
        <p class="mb-0 fw-semibold"><?= html_escape($trx->nama_event) ?></p>
        <p class="mb-0"><?= rentang_tgl($trx->tanggal_mulai, $trx->tanggal_selesai) ?><?= $trx->jam_mulai ? ', '.substr($trx->jam_mulai, 0, 5).' WIB' : '' ?></p>
        <p class="mb-0 text-muted"><?= html_escape($trx->nama_lokasi.', '.$trx->kota) ?></p>
      </div>
    </div>

    <div class="table-responsive mt-4">
      <table class="table mb-0">
        <thead><tr><th>Jenis Tiket</th><th class="text-end">Harga</th><th class="text-end">Jumlah</th><th class="text-end">Subtotal</th></tr></thead>
        <tbody><tr>
          <td><?= html_escape($trx->nama_tiket) ?></td>
          <td class="text-end"><?= rupiah($trx->harga_satuan) ?></td>
          <td class="text-end"><?= $trx->jumlah ?></td>
          <td class="text-end"><?= rupiah($trx->total_harga) ?></td>
        </tr></tbody>
        <tfoot><tr><th colspan="3" class="text-end">Total Pembayaran</th><th class="text-end fs-5"><?= rupiah($trx->total_harga) ?></th></tr></tfoot>
      </table>
    </div>

    <dl class="row mt-4 mb-0">
      <dt class="col-sm-4">Tanggal pesan</dt><dd class="col-sm-8"><?= tgl_indo($trx->tanggal_pesan, TRUE) ?></dd>
      <dt class="col-sm-4">Tanggal bayar</dt><dd class="col-sm-8"><?= tgl_indo($trx->tanggal_bayar, TRUE) ?></dd>
      <dt class="col-sm-4">Metode bayar</dt><dd class="col-sm-8"><?= html_escape($trx->metode_bayar) ?: '-' ?></dd>
      <?php if ($trx->jumlah_bayar !== NULL): ?>
      <dt class="col-sm-4">Uang diterima</dt><dd class="col-sm-8"><?= rupiah($trx->jumlah_bayar) ?></dd>
      <dt class="col-sm-4">Kembalian</dt><dd class="col-sm-8"><?= rupiah($trx->kembalian) ?></dd>
      <?php endif; ?>
      <?php if ($trx->bukti_bayar): ?>
      <dt class="col-sm-4">Bukti pembayaran</dt><dd class="col-sm-8">
        <?php $u = site_url('kasir/bukti/'.$trx->id_transaksi); ?>
        <?php if (preg_match('/\.pdf$/i', $trx->bukti_bayar)): ?>
          <a class="btn btn-outline-primary btn-sm" href="<?= $u ?>" target="_blank" rel="noopener"><i class="bi bi-file-earmark-pdf"></i> Lihat bukti (PDF)</a>
        <?php else: ?>
          <a href="<?= $u ?>" target="_blank" rel="noopener"><img src="<?= $u ?>" alt="Bukti pembayaran" class="img-thumbnail" style="max-width:220px;max-height:260px"></a>
        <?php endif; ?>
      </dd>
      <?php endif; ?>
      <dt class="col-sm-4">Catatan</dt><dd class="col-sm-8"><?= html_escape($trx->catatan) ?: '-' ?></dd>
    </dl>

    <?php if ($trx->status === 'menunggu'): ?>
      <div class="mt-4 no-print"><a class="btn btn-success" href="<?= site_url('kasir/bayar/'.$trx->id_transaksi) ?>"><i class="bi bi-cash-coin"></i> Bayar Sekarang</a></div>
    <?php endif; ?>
  </section>
</div></div>
