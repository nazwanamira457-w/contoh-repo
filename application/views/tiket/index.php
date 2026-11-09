<section class="panel mt-3">
  <div class="panel-header">
    <div><h2 class="h5 mb-1 section-title"><i class="bi bi-ticket-perforated"></i><span>Daftar Jenis Tiket</span></h2></div>
    <input class="form-control form-control-sm w-auto" type="search" placeholder="Cari tiket / event..." data-table-search="tbl" aria-label="Cari">
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0" id="tbl">
      <thead><tr><th>Event</th><th>Tiket</th><th class="text-end">Harga</th><th class="text-end">Kuota</th><th class="text-end">Terjual</th><th class="text-end">Sisa</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><a href="<?= site_url('event/detail/'.$r->id_event) ?>"><?= html_escape($r->nama_event) ?></a>
            <?php if (event_selesai($r->tanggal_selesai)): ?><span class="badge text-bg-secondary">Selesai</span><?php endif; ?></td>
          <td class="fw-semibold"><?= html_escape($r->nama_tiket) ?></td>
          <td class="text-end"><?= rupiah($r->harga) ?></td>
          <td class="text-end"><?= $r->kuota ?></td>
          <td class="text-end"><?= $r->terjual ?></td>
          <td class="text-end"><span class="badge <?= $r->sisa > 0 ? 'text-bg-success' : 'text-bg-danger' ?>"><?= $r->sisa ?></span></td>
          <td class="text-end table-actions text-nowrap">
            <a class="btn btn-light btn-sm" href="<?= site_url('tiket/ubah/'.$r->id_tiket) ?>"><i class="bi bi-pencil"></i></a>
            <?= form_open('tiket/hapus/'.$r->id_tiket, array('onsubmit' => "return confirm('Hapus jenis tiket ini?')")) ?>
              <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i></button>
            <?= form_close() ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if ( ! $rows): ?><tr><td colspan="7" class="text-center text-muted">Belum ada jenis tiket.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
