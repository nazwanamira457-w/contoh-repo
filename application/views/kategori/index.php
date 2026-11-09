<section class="panel mt-3">
  <div class="panel-header">
    <div><h2 class="h5 mb-1 section-title"><i class="bi bi-tags"></i><span>Daftar Kategori</span></h2></div>
    <input class="form-control form-control-sm w-auto" type="search" placeholder="Cari kategori..." data-table-search="tbl" aria-label="Cari">
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0" id="tbl">
      <thead><tr><th>#</th><th>Kategori</th><th>Keterangan</th><th class="text-center">Event</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $i => $r): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td class="fw-semibold"><?= html_escape($r->nama_kategori) ?></td>
          <td><?= html_escape($r->keterangan) ?: '-' ?></td>
          <td class="text-center"><?= (int) $r->jml_event ?></td>
          <td class="text-end table-actions">
            <a class="btn btn-light btn-sm" href="<?= site_url('kategori/ubah/'.$r->id_kategori) ?>"><i class="bi bi-pencil"></i></a>
            <?= form_open('kategori/hapus/'.$r->id_kategori, array('onsubmit' => "return confirm('Hapus kategori ini?')")) ?>
              <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash"></i></button>
            <?= form_close() ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if ( ! $rows): ?><tr><td colspan="5" class="text-center text-muted">Belum ada data.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
