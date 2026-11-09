<section class="panel mt-3">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead><tr><th>Kode</th><th>Tanggal</th><th>Event / Tiket</th><th class="text-end">Total</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $t): ?>
        <tr>
          <td class="text-nowrap"><a class="fw-semibold" href="<?= site_url('kasir/detail/'.$t->id_transaksi) ?>"><?= html_escape($t->kode_transaksi) ?></a></td>
          <td class="text-nowrap"><?= tgl_indo($t->tanggal_pesan) ?></td>
          <td><?= html_escape($t->nama_event) ?><br><small class="text-muted"><?= html_escape($t->nama_tiket) ?> &times; <?= $t->jumlah ?></small></td>
          <td class="text-end text-nowrap"><?= rupiah($t->total_harga) ?></td>
          <td><?= badge_status($t->status) ?></td>
          <td class="text-end table-actions text-nowrap">
            <?php if ($t->status === 'menunggu'): ?>
              <a class="btn btn-success btn-sm" href="<?= site_url('kasir/bayar/'.$t->id_transaksi) ?>"><i class="bi bi-cash-coin"></i> Bayar</a>
              <?= form_open('kasir/batal/'.$t->id_transaksi, array('onsubmit' => "return confirm('Batalkan pesanan ini?')")) ?>
                <button class="btn btn-outline-danger btn-sm" type="submit" title="Batalkan"><i class="bi bi-x-circle"></i></button>
              <?= form_close() ?>
            <?php else: ?>
              <a class="btn btn-light btn-sm" href="<?= site_url('kasir/detail/'.$t->id_transaksi) ?>"><i class="bi bi-eye"></i></a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if ( ! $rows): ?><tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan. <a href="<?= site_url('loket') ?>">Pesan tiket di Loket</a>.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
