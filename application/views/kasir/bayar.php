<div class="row g-3 mt-1">
  <div class="col-12 col-xl-7">
    <?= form_open_multipart('kasir/bayar/'.$trx->id_transaksi, array('class' => 'panel', 'novalidate' => 'novalidate')) ?>
      <h2 class="h5 section-title mb-3"><i class="bi bi-credit-card"></i><span>Metode Pembayaran</span></h2>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="metode">Metode</label>
          <select class="form-select<?= inv('metode') ?>" id="metode" name="metode">
            <option value="">-- Pilih metode --</option>
            <?php foreach (array('Transfer Bank', 'QRIS', 'E-Wallet', 'Tunai') as $m): ?>
              <option value="<?= $m ?>"<?= sel(set_value('metode'), $m) ?>><?= $m ?></option>
            <?php endforeach; ?>
          </select>
          <?= err('metode') ?>
        </div>
        <div class="col-md-6" id="box-tunai" style="display:none">
          <label class="form-label" for="jumlah_bayar">Uang diterima (Rp)</label>
          <input class="form-control<?= inv('jumlah_bayar') ?>" id="jumlah_bayar" name="jumlah_bayar" type="number" min="0" step="any" value="<?= set_value('jumlah_bayar') ?>" placeholder="<?= (float) $trx->total_harga ?>">
          <?= err('jumlah_bayar') ?>
          <div class="form-text"><a href="#" id="uang-pas">Uang pas</a></div>
        </div>
        <div class="col-12" id="box-kembalian" style="display:none">
          <div class="total-box"><small class="text-muted" id="label-kembalian">Kembalian</small><div class="total-value" id="nilai-kembalian">Rp 0</div></div>
        </div>
        <div class="col-12" id="box-nontunai" style="display:none">
          <div class="form-text mb-2">Pembayaran non-tunai otomatis sebesar total: <strong><?= rupiah($trx->total_harga) ?></strong>.</div>
          <label class="form-label" for="bukti">Bukti pembayaran</label>
          <input class="form-control" id="bukti" name="bukti" type="file" accept="image/jpeg,image/png,application/pdf">
          <div class="form-text">Unggah screenshot atau foto bukti transfer. Format JPG, PNG, atau PDF, maksimal 2 MB.</div>
        </div>
      </div>
      <div class="d-flex justify-content-end gap-2 mt-4">
        <a class="btn btn-outline-secondary" href="<?= site_url('kasir') ?>">Nanti saja</a>
        <button class="btn btn-success" type="submit"><i class="bi bi-check2-circle"></i> Bayar Sekarang</button>
      </div>
    <?= form_close() ?>
  </div>
  <div class="col-12 col-xl-5">
    <section class="panel">
      <h2 class="h5 section-title mb-3"><i class="bi bi-receipt"></i><span>Rincian Pesanan</span></h2>
      <p class="mb-1 fw-semibold"><?= html_escape($trx->nama_event) ?></p>
      <p class="text-muted"><?= rentang_tgl($trx->tanggal_mulai, $trx->tanggal_selesai) ?></p>
      <dl class="row mb-3">
        <dt class="col-6">Tiket</dt><dd class="col-6 text-end"><?= html_escape($trx->nama_tiket) ?></dd>
        <dt class="col-6">Harga satuan</dt><dd class="col-6 text-end"><?= rupiah($trx->harga_satuan) ?></dd>
        <dt class="col-6">Jumlah</dt><dd class="col-6 text-end"><?= $trx->jumlah ?></dd>
      </dl>
      <div class="total-box"><small class="text-muted">Total yang harus dibayar</small><div class="total-value"><?= rupiah($trx->total_harga) ?></div></div>
    </section>
  </div>
</div>
<?php ob_start(); ?>
<script>
(function(){
  var total=<?= (float) $trx->total_harga ?>;
  var m=document.getElementById('metode'), u=document.getElementById('jumlah_bayar');
  var bt=document.getElementById('box-tunai'), bk=document.getElementById('box-kembalian'), bn=document.getElementById('box-nontunai');
  var lk=document.getElementById('label-kembalian'), nk=document.getElementById('nilai-kembalian');
  function rp(x){return 'Rp '+Math.round(x).toLocaleString('id-ID');}
  function hitung(){
    var v=parseFloat(u.value);
    if(isNaN(v)){lk.textContent='Kembalian';nk.textContent='Rp 0';return;}
    if(v<total){lk.textContent='Uang kurang';nk.textContent=rp(total-v);}
    else{lk.textContent='Kembalian';nk.textContent=rp(v-total);}
  }
  function ubah(){
    var t=(m.value==='Tunai');
    bt.style.display=t?'':'none'; bk.style.display=t?'':'none';
    bn.style.display=(m.value!==''&&!t)?'':'none';
    if(!t){u.value='';} else {document.getElementById('bukti').value='';} hitung();
  }
  m.addEventListener('change',ubah); u.addEventListener('input',hitung);
  document.getElementById('uang-pas').addEventListener('click',function(e){e.preventDefault();u.value=total;hitung();});
  ubah();
})();
</script>
<?php $this->load->vars('extra_js', ob_get_clean()); ?>
