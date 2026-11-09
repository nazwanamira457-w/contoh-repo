<section class="panel mt-3">
  <div class="panel-header">
    <div><h2 class="h5 mb-1 section-title"><i class="bi bi-people"></i><span>Daftar Pemesan</span></h2></div>
    <input class="form-control form-control-sm w-auto" type="search" placeholder="Cari pemesan..." data-table-search="tbl" aria-label="Cari">
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0" id="tbl">
      <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>No. HP</th><th>Alamat</th><th class="text-center">Transaksi</th><th class="text-end">Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $i => $r): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td class="fw-semibold"><?= html_escape($r->nama) ?></td>
          <td><?= html_escape($r->email) ?></td>
          <td><?= html_escape($r->no_hp) ?></td>
          <td><?= html_escape($r->alamat) ?: '-' ?></td>
          <td class="text-center"><?= (int) $r->jml_transaksi ?></td>
          <td class="text-end table-actions">
            <a class="btn btn-light btn-sm" title="Pesan tiket" href="<?= site_url('transaksi/tambah?id_pemesan='.$r->id_pemesan) ?>"><i class="bi bi-cart-plus"></i></a>
            <a class="btn btn-light btn-sm" href="<?= site_url('pemesan/ubah/'.$r->id_pemesan) ?>"><i class="bi bi-pencil"></i></a>
            <?= form_open('pemesan/hapus/'.$r->id_pemesan, array('onsubmit' => "return confirm('Hapus pemesan ini?')")) ?>
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
