<?php
$tot = array('kuota' => 0, 'terjual' => 0, 'sisa' => 0, 'pendapatan' => 0, 'menunggu' => 0);
foreach ($rows as $r) { $tot['kuota'] += $r->kuota; $tot['terjual'] += $r->terjual; $tot['sisa'] += $r->sisa; $tot['pendapatan'] += $r->pendapatan; $tot['menunggu'] += $r->menunggu_bayar; }
$tot_pct = $tot['kuota'] > 0 ? round($tot['terjual'] / $tot['kuota'] * 100) : 0;
?>
<section class="row g-3 mt-1">
  <div class="col-6 col-xl-3"><article class="metric-card metric-primary"><div class="metric-top"><span class="metric-label">Total Kuota</span></div><div class="metric-value"><?= number_format($tot['kuota'], 0, ',', '.') ?></div></article></div>
  <div class="col-6 col-xl-3"><article class="metric-card metric-success"><div class="metric-top"><span class="metric-label">Tiket Terjual</span></div><div class="metric-value"><?= number_format($tot['terjual'], 0, ',', '.') ?></div><div class="metric-meta"><span><?= $tot_pct ?>% dari kuota</span></div></article></div>
  <div class="col-6 col-xl-3"><article class="metric-card metric-warning"><div class="metric-top"><span class="metric-label">Tiket Tersisa</span></div><div class="metric-value"><?= number_format($tot['sisa'], 0, ',', '.') ?></div></article></div>
  <div class="col-6 col-xl-3"><article class="metric-card metric-danger"><div class="metric-top"><span class="metric-label">Pendapatan (Lunas)</span></div><div class="metric-value"><?= rupiah($tot['pendapatan']) ?></div></article></div>
</section>

<section class="panel mt-3">
  <?= form_open('laporan', array('method' => 'get', 'class' => 'row g-2 align-items-end mb-3 no-print')) ?>
    <div class="col-12 col-md-6 col-lg-4">
      <label class="form-label small mb-1" for="id_event">Filter event</label>
      <select class="form-select" id="id_event" name="id_event" onchange="this.form.submit()">
        <option value="">Semua event</option>
        <?php foreach ($events as $e): ?><option value="<?= $e->id_event ?>" <?= $id_event == $e->id_event ? 'selected' : '' ?>><?= html_escape($e->nama_event) ?></option><?php endforeach; ?>
      </select>
    </div>
  <?= form_close() ?>

  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead><tr><th>Event</th><th>Tiket</th><th class="text-end">Harga</th><th class="text-end">Kuota</th><th class="text-end">Terjual</th><th class="text-end">Sisa</th><th style="min-width:130px">Terjual %</th><th class="text-end">Pendapatan</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r):
        $pct = $r->kuota > 0 ? round($r->terjual / $r->kuota * 100) : 0; ?>
        <tr>
          <td><?= html_escape($r->nama_event) ?><?php if (event_selesai($r->tanggal_selesai)): ?> <span class="badge text-bg-secondary">Selesai</span><?php endif; ?></td>
          <td class="fw-semibold"><?= html_escape($r->nama_tiket) ?></td>
          <td class="text-end"><?= rupiah($r->harga) ?></td>
          <td class="text-end"><?= $r->kuota ?></td>
          <td class="text-end"><?= $r->terjual ?><?php if ($r->menunggu_bayar > 0): ?><br><small class="text-muted"><?= $r->menunggu_bayar ?> belum bayar</small><?php endif; ?></td>
          <td class="text-end"><span class="badge <?= $r->sisa > 0 ? 'text-bg-success' : 'text-bg-danger' ?>"><?= $r->sisa ?></span></td>
          <td>
            <div class="progress stock-bar" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar <?= $pct >= 90 ? 'bg-danger' : ($pct >= 60 ? 'bg-warning' : 'bg-success') ?>" style="width: <?= $pct ?>%"></div></div>
            <small class="text-muted"><?= $pct ?>%</small>
          </td>
          <td class="text-end text-nowrap"><?= rupiah($r->pendapatan) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if ( ! $rows): ?><tr><td colspan="8" class="text-center text-muted py-4">Belum ada data tiket.</td></tr><?php endif; ?>
      </tbody>
      <?php if ($rows): ?>
      <tfoot><tr class="fw-bold"><td colspan="3" class="text-end">Total</td><td class="text-end"><?= $tot['kuota'] ?></td><td class="text-end"><?= $tot['terjual'] ?></td><td class="text-end"><?= $tot['sisa'] ?></td><td><?= $tot_pct ?>%</td><td class="text-end"><?= rupiah($tot['pendapatan']) ?></td></tr></tfoot>
      <?php endif; ?>
    </table>
  </div>
</section>
