<section class="panel mt-3">
  <?= form_open('transaksi', array('method' => 'get', 'class' => 'row g-2 align-items-end mb-3')) ?>
    <div class="col-12 col-md-4">
      <label class="form-label small mb-1" for="q">Cari transaksi</label>
      <input class="form-control" id="q" name="q" value="<?= html_escape($f['q']) ?>" placeholder="Kode, pemesan, email, no. HP, event, tiket">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label small mb-1" for="id_event">Event</label>
      <select class="form-select" id="id_event" name="id_event">
        <option value="">Semua event</option>
        <?php foreach ($events as $e): ?><option value="<?= $e->id_event ?>" <?= $f['id_event'] == $e->id_event ? 'selected' : '' ?>><?= html_escape($e->nama_event) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label small mb-1" for="status">Status</label>
      <select class="form-select" id="status" name="status">
        <option value="">Semua status</option>
        <?php foreach (array('menunggu' => 'Menunggu Pembayaran', 'lunas' => 'Lunas', 'batal' => 'Batal') as $v => $t): ?>
          <option value="<?= $v ?>" <?= $f['status'] === $v ? 'selected' : '' ?>><?= $t ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-12 col-md-2 d-flex gap-2">
      <button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-search"></i> Cari</button>
      <a class="btn btn-outline-secondary" href="<?= site_url('transaksi') ?>" title="Reset"><i class="bi bi-x-lg"></i></a>
    </div>
  <?= form_close() ?>

  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead><tr><th>Kode</th><th>Tanggal</th><th>Pemesan</th><th>Event / Tiket</th><th class="text-end">Jml</th><th class="text-end">Total</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $t): ?>
        <tr>
          <td><a class="fw-semibold" href="<?= site_url('transaksi/detail/'.$t->id_transaksi) ?>"><?= html_escape($t->kode_transaksi) ?></a></td>
          <td class="text-nowrap"><?= tgl_indo($t->tanggal_pesan, TRUE) ?></td>
          <td><?= html_escape($t->nama_pemesan) ?><br><small class="text-muted"><?= html_escape($t->no_hp) ?></small></td>
          <td><?= html_escape($t->nama_event) ?><br><small class="text-muted"><?= html_escape($t->nama_tiket) ?></small></td>
          <td class="text-end"><?= $t->jumlah ?></td>
          <td class="text-end text-nowrap"><?= rupiah($t->total_harga) ?></td>
          <td><?= badge_status($t->status) ?></td>
          <td class="text-end table-actions text-nowrap">
            <a class="btn btn-light btn-sm" title="Detail" href="<?= site_url('transaksi/detail/'.$t->id_transaksi) ?>"><i class="bi bi-eye"></i></a>
            <?php if ($t->status === 'menunggu'): ?>
              <?= form_open('transaksi/status/'.$t->id_transaksi.'/lunas', array('onsubmit' => "return confirm('Tandai transaksi ini sudah dibayar (lunas)?')")) ?>
                <input type="hidden" name="kembali" value="list">
                <button class="btn btn-outline-success btn-sm" title="Tandai lunas" type="submit"><i class="bi bi-check2-circle"></i></button>
              <?= form_close() ?>
            <?php endif; ?>
            <?php if ($t->status !== 'batal'): ?>
              <?= form_open('transaksi/status/'.$t->id_transaksi.'/batal', array('onsubmit' => "return confirm('Batalkan transaksi ini? Kuota akan dikembalikan.')")) ?>
                <input type="hidden" name="kembali" value="list">
                <button class="btn btn-outline-danger btn-sm" title="Batalkan" type="submit"><i class="bi bi-x-circle"></i></button>
              <?= form_close() ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if ( ! $rows): ?><tr><td colspan="8" class="text-center text-muted py-4">Tidak ada transaksi yang cocok.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
    <small class="text-muted"><?= $total ? 'Menampilkan '.($offset + 1).'-'.($offset + count($rows)).' dari '.$total.' transaksi' : '0 transaksi' ?></small>
    <?= $links ?>
  </div>
</section>
