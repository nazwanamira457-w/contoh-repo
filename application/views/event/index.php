<section class="panel mt-3">
  <div class="panel-header">
    <div><h2 class="h5 mb-1 section-title"><i class="bi bi-calendar-event"></i><span>Daftar Wisata / Event</span></h2></div>
    <input class="form-control form-control-sm w-auto" type="search" placeholder="Cari event..." data-table-search="tbl" aria-label="Cari">
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0" id="tbl">
      <thead><tr><th>Event</th><th>Tanggal</th><th>Lokasi</th><th>Waktu</th><th class="text-center">Terjual / Kuota</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): list($lbl, $cls) = status_waktu_event($r->tanggal_mulai, $r->tanggal_selesai); ?>
        <tr>
          <td>
            <a class="fw-semibold" href="<?= site_url('event/detail/'.$r->id_event) ?>"><?= html_escape($r->nama_event) ?></a><br>
            <small class="text-muted"><?= html_escape($r->nama_kategori) ?></small>
          </td>
          <td class="text-nowrap"><?= rentang_tgl($r->tanggal_mulai, $r->tanggal_selesai) ?><?= $r->jam_mulai ? '<br><small class="text-muted">'.substr($r->jam_mulai, 0, 5).' WIB</small>' : '' ?></td>
          <td><?= html_escape($r->nama_lokasi) ?><br><small class="text-muted"><?= html_escape($r->kota) ?></small></td>
          <td><span class="badge <?= $cls ?>"><?= $lbl ?></span></td>
          <td class="text-center"><?= (int) $r->total_terjual ?> / <?= (int) $r->total_kuota ?></td>
          <td><?= badge_status($r->status) ?></td>
          <td class="text-end table-actions text-nowrap">
            <a class="btn btn-light btn-sm" title="Detail" href="<?= site_url('event/detail/'.$r->id_event) ?>"><i class="bi bi-eye"></i></a>
            <a class="btn btn-light btn-sm" title="Ubah" href="<?= site_url('event/ubah/'.$r->id_event) ?>"><i class="bi bi-pencil"></i></a>
            <?= form_open('event/hapus/'.$r->id_event, array('onsubmit' => "return confirm('Hapus event beserta jenis tiketnya?')")) ?>
              <button class="btn btn-outline-danger btn-sm" title="Hapus" type="submit"><i class="bi bi-trash"></i></button>
            <?= form_close() ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if ( ! $rows): ?><tr><td colspan="7" class="text-center text-muted">Belum ada event.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
