<section class="panel mt-3">
  <div class="panel-header">
    <div><h2 class="h5 mb-1 section-title"><i class="bi bi-geo-alt"></i><span>Daftar Lokasi</span></h2></div>
    <input class="form-control form-control-sm w-auto" type="search" placeholder="Cari lokasi..." data-table-search="tbl" aria-label="Cari">
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0" id="tbl">
      <thead><tr><th>#</th><th>Lokasi</th><th>Alamat</th><th>Kota</th><th class="text-end">Kapasitas</th><th class="text-center">Event</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $i => $r): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td class="fw-semibold"><?= html_escape($r->nama_lokasi) ?></td>
          <td><?= html_escape($r->alamat) ?: '-' ?></td>
          <td><?= html_escape($r->kota) ?></td>
          <td class="text-end"><?= $r->kapasitas ? number_format($r->kapasitas, 0, ',', '.') : '-' ?></td>
          <td class="text-center"><?= (int) $r->jml_event ?></td>
          <td class="text-end table-actions">
            <a class="btn btn-light btn-sm" href="<?= site_url('lokasi/ubah/'.$r->id_lokasi) ?>"><i class="bi bi-pencil"></i></a>
            <?= form_open('lokasi/hapus/'.$r->id_lokasi, array('onsubmit' => "return confirm('Hapus lokasi ini?')")) ?>
              <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i></button>
            <?= form_close() ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if ( ! $rows): ?><tr><td colspan="7" class="text-center text-muted">Belum ada data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
