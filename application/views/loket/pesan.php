<div class="row g-3 mt-1">
  <div class="col-12 col-xl-7">
    <?= form_open('loket/pesan/'.$t->id_tiket, array('class' => 'panel', 'id' => 'formPesan', 'novalidate' => 'novalidate')) ?>
      <h2 class="h5 section-title mb-3"><i class="bi bi-ticket-perforated"></i><span><?= html_escape($t->nama_event) ?></span></h2>
      <p class="text-muted"><?= rentang_tgl($t->tanggal_mulai, $t->tanggal_selesai) ?> &middot; Tiket <strong><?= html_escape($t->nama_tiket) ?></strong> &middot; <?= rupiah($t->harga) ?> / tiket</p>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="jumlah">Jumlah tiket</label>
          <input class="form-control<?= inv('jumlah') ?>" id="jumlah" name="jumlah" type="number" min="1" step="1" value="<?= set_value('jumlah', 1) ?>">
          <?= err('jumlah') ?>
          <div class="form-text">Sisa tiket: <strong><?= (int) $t->sisa ?></strong></div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="catatan">Catatan (opsional)</label>
          <input class="form-control" id="catatan" name="catatan" value="<?= set_value('catatan') ?>">
        </div>
      </div>
      <input type="hidden" name="total_bayar" id="total_bayar" value="0">
      <?= err('total_bayar') ?>
      <div class="alert alert-danger py-2 mt-3 mb-0" id="peringatan" hidden></div>
      <div class="d-flex justify-content-end gap-2 mt-4">
        <a class="btn btn-outline-secondary" href="<?= site_url('loket') ?>">Batal</a>
        <button class="btn btn-primary" type="submit"><i class="bi bi-cart-check"></i> Pesan &amp; Lanjut Bayar</button>
      </div>
    <?= form_close() ?>
  </div>
  <div class="col-12 col-xl-5">
    <section class="panel">
      <h2 class="h5 section-title mb-3"><i class="bi bi-calculator"></i><span>Ringkasan</span></h2>
      <dl class="row mb-3">
        <dt class="col-6">Harga satuan</dt><dd class="col-6 text-end mb-1"><?= rupiah($t->harga) ?></dd>
        <dt class="col-6">Jumlah</dt><dd class="col-6 text-end mb-1" id="txtJumlah">0</dd>
      </dl>
      <div class="total-box"><small class="text-muted">Total pembayaran</small><div class="total-value" id="txtTotal">Rp 0</div></div>
      <p class="text-muted small mt-3 mb-0">Setelah memesan, bayar di <strong>Kasir</strong> agar pesanan menjadi Lunas.</p>
    </section>
  </div>
</div>
<?php ob_start(); ?>
<script>
(function () {
  var harga = <?= (float) $t->harga ?>, sisa = <?= (int) $t->sisa ?>;
  var jumlah = document.getElementById('jumlah');
  function hitung() {
    var j = parseInt(jumlah.value, 10), ok = !isNaN(j) && j > 0, total = ok ? harga * j : 0;
    document.getElementById('txtJumlah').textContent = ok ? j : 0;
    document.getElementById('txtTotal').textContent = 'Rp ' + Math.round(total).toLocaleString('id-ID');
    document.getElementById('total_bayar').value = total;
    var w = document.getElementById('peringatan'), m = '';
    if (ok && j > sisa) m = 'Jumlah melebihi sisa tiket (' + sisa + '). Pesanan akan ditolak.';
    else if (jumlah.value !== '' && !ok) m = 'Jumlah tiket harus lebih dari nol.';
    w.hidden = m === ''; w.textContent = m;
  }
  jumlah.addEventListener('input', hitung); hitung();
})();
</script>
<?php $this->load->vars('extra_js', ob_get_clean()); ?>
