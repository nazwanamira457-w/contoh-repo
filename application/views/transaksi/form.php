<?php
$pre_p = set_value('id_pemesan', (int) $this->input->get('id_pemesan') ?: '');
$mode_post = $this->input->post('mode_pemesan');
$mode_baru = $mode_post ? ($mode_post !== 'lama') : ($pre_p === '' || $pre_p === '0');
$sel_tiket = set_value('id_tiket', $pre_tiket ?: '');
?>
<?php if ( ! $tikets): ?>
  <div class="alert alert-warning mt-3"><i class="bi bi-exclamation-triangle"></i> Belum ada tiket yang bisa dipesan. Pastikan ada event <strong>aktif</strong> yang <strong>belum selesai</strong> dan memiliki jenis tiket.</div>
<?php endif; ?>

<?= form_open('transaksi/tambah', array('id' => 'formTrx', 'novalidate' => 'novalidate')) ?>
<div class="row g-3 mt-1">
  <div class="col-12 col-xl-7">
    <section class="panel mb-3">
      <h2 class="h5 section-title mb-3"><i class="bi bi-ticket-perforated"></i><span>Tiket</span></h2>
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label" for="id_tiket">Jenis tiket</label>
          <select class="form-select<?= inv('id_tiket') ?>" id="id_tiket" name="id_tiket">
            <option value="" data-harga="0" data-sisa="0">-- Pilih tiket --</option>
            <?php $grup = NULL; foreach ($tikets as $t):
              if ($grup !== $t->id_event) { if ($grup !== NULL) echo '</optgroup>'; echo '<optgroup label="'.html_escape($t->nama_event.' ('.rentang_tgl($t->tanggal_mulai, $t->tanggal_selesai).')').'">'; $grup = $t->id_event; } ?>
              <option value="<?= $t->id_tiket ?>" data-harga="<?= (float) $t->harga ?>" data-sisa="<?= (int) $t->sisa ?>" <?= (string) $sel_tiket === (string) $t->id_tiket ? 'selected' : '' ?>>
                <?= html_escape($t->nama_tiket.' - '.rupiah($t->harga).' (sisa '.$t->sisa.')') ?></option>
            <?php endforeach; if ($grup !== NULL) echo '</optgroup>'; ?>
          </select>
          <?= err('id_tiket') ?>
          <div class="form-text" id="infoSisa">Pilih tiket untuk melihat sisa kuota.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="jumlah">Jumlah tiket</label>
          <input class="form-control<?= inv('jumlah') ?>" id="jumlah" name="jumlah" type="number" min="1" step="1" value="<?= set_value('jumlah', 1) ?>">
          <?= err('jumlah') ?>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="status">Status pembayaran</label>
          <select class="form-select<?= inv('status') ?>" id="status" name="status">
            <option value="menunggu"<?= sel(set_value('status', 'menunggu'), 'menunggu') ?>>Menunggu Pembayaran</option>
            <option value="lunas"<?= sel(set_value('status', 'menunggu'), 'lunas') ?>>Lunas</option>
          </select>
          <?= err('status') ?>
        </div>
        <div class="col-12">
          <label class="form-label" for="catatan">Catatan (opsional)</label>
          <input class="form-control<?= inv('catatan') ?>" id="catatan" name="catatan" value="<?= set_value('catatan') ?>">
          <?= err('catatan') ?>
        </div>
      </div>
    </section>

    <section class="panel">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 section-title mb-0"><i class="bi bi-person"></i><span>Pemesan</span></h2>
        <div class="btn-group btn-group-sm" role="group" aria-label="Jenis pemesan">
          <input type="radio" class="btn-check" name="mode_pemesan" id="modeLama" value="lama" <?= $mode_baru ? '' : 'checked' ?>>
          <label class="btn btn-outline-primary" for="modeLama">Pemesan terdaftar</label>
          <input type="radio" class="btn-check" name="mode_pemesan" id="modeBaru" value="baru" <?= $mode_baru ? 'checked' : '' ?>>
          <label class="btn btn-outline-primary" for="modeBaru">Pemesan baru</label>
        </div>
      </div>

      <div id="boxLama" <?= $mode_baru ? 'hidden' : '' ?>>
        <label class="form-label" for="id_pemesan">Pilih pemesan</label>
        <select class="form-select<?= inv('id_pemesan') ?>" id="id_pemesan" name="id_pemesan">
          <option value="">-- Pilih pemesan --</option>
          <?php foreach ($pemesan as $p): ?>
            <option value="<?= $p->id_pemesan ?>" <?= (string) $pre_p === (string) $p->id_pemesan ? 'selected' : '' ?>><?= html_escape($p->nama.' - '.$p->no_hp) ?></option>
          <?php endforeach; ?>
        </select>
        <?= err('id_pemesan') ?>
      </div>

      <div id="boxBaru" class="row g-3" <?= $mode_baru ? '' : 'hidden' ?>>
        <div class="col-md-6">
          <label class="form-label" for="nama">Nama lengkap</label>
          <input class="form-control<?= inv('nama') ?>" id="nama" name="nama" value="<?= set_value('nama') ?>">
          <?= err('nama') ?>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="email">Email</label>
          <input class="form-control<?= inv('email') ?>" id="email" name="email" type="email" value="<?= set_value('email') ?>">
          <?= err('email') ?>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="no_hp">No. HP</label>
          <input class="form-control<?= inv('no_hp') ?>" id="no_hp" name="no_hp" inputmode="tel" placeholder="08xxxxxxxxxx" value="<?= set_value('no_hp') ?>">
          <?= err('no_hp') ?>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="alamat">Alamat (opsional)</label>
          <input class="form-control" id="alamat" name="alamat" value="<?= set_value('alamat') ?>">
        </div>
      </div>
    </section>
  </div>

  <div class="col-12 col-xl-5">
    <section class="panel position-sticky" style="top:1rem">
      <h2 class="h5 section-title mb-3"><i class="bi bi-calculator"></i><span>Ringkasan Pembayaran</span></h2>
      <dl class="row mb-3">
        <dt class="col-6">Harga satuan</dt><dd class="col-6 text-end mb-1" id="txtHarga">Rp 0</dd>
        <dt class="col-6">Jumlah</dt><dd class="col-6 text-end mb-1" id="txtJumlah">0</dd>
      </dl>
      <div class="total-box">
        <small class="text-muted">Total pembayaran</small>
        <div class="total-value" id="txtTotal">Rp 0</div>
      </div>
      <input type="hidden" name="total_bayar" id="total_bayar" value="<?= set_value('total_bayar', 0) ?>">
      <?= err('total_bayar') ?>
      <div class="alert alert-danger py-2 mt-3 mb-0" id="peringatan" hidden></div>
      <button class="btn btn-primary w-100 mt-3" type="submit"><i class="bi bi-cart-check"></i> Simpan Pemesanan</button>
      <p class="text-muted small mt-2 mb-0">Kuota langsung berkurang setelah pemesanan tersimpan, dan dikembalikan jika pesanan dibatalkan.</p>
    </section>
  </div>
</div>
<?= form_close() ?>

<?php ob_start(); ?>
<script>
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var tiket = $('id_tiket'), jumlah = $('jumlah');
  var rp = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };

  function hitung() {
    var opt = tiket.options[tiket.selectedIndex];
    var harga = parseFloat(opt.getAttribute('data-harga')) || 0;
    var sisa = parseInt(opt.getAttribute('data-sisa'), 10) || 0;
    var jml = parseInt(jumlah.value, 10);
    var valid = !isNaN(jml) && jml > 0;
    var total = valid ? harga * jml : 0;

    $('txtHarga').textContent = rp(harga);
    $('txtJumlah').textContent = valid ? jml : 0;
    $('txtTotal').textContent = rp(total);
    $('total_bayar').value = total;
    $('infoSisa').textContent = tiket.value ? 'Sisa kuota: ' + sisa + ' tiket.' : 'Pilih tiket untuk melihat sisa kuota.';

    var w = $('peringatan'), msg = '';
    if (tiket.value && valid && jml > sisa) msg = 'Jumlah melebihi sisa kuota (' + sisa + '). Pemesanan akan ditolak.';
    else if (jumlah.value !== '' && !valid) msg = 'Jumlah tiket harus lebih dari nol.';
    w.hidden = msg === ''; w.textContent = msg;
  }
  tiket.addEventListener('change', hitung);
  jumlah.addEventListener('input', hitung);

  function mode() {
    var baru = $('modeBaru').checked;
    $('boxBaru').hidden = !baru; $('boxLama').hidden = baru;
    if (baru) $('id_pemesan').value = '';
  }
  $('modeBaru').addEventListener('change', mode);
  $('modeLama').addEventListener('change', mode);
  hitung();
})();
</script>
<?php $this->load->vars('extra_js', ob_get_clean()); ?>
