        </div>
      </main>

      <footer class="admin-footer">
        <div class="container-fluid px-3 px-lg-4">
          <span>&copy; <?= date('Y') ?> TiketLokal &mdash; Sistem Tiketing Wisata &amp; Event Lokal.</span>
          <span>Template <a target="_blank" rel="noopener" class="fw-bold text-success" href="https://themewagon.com">adminHMD</a></span>
        </div>
      </footer>
    </div>
  </div>

  <script>window.adminHMDUser = { name: <?= json_encode($user_nama) ?>, workspace: <?= json_encode($user_role === 'pembeli' ? 'Pembeli' : 'Pengelola') ?>, avatar: <?= json_encode(base_url('assets/images/avatar/avatar.jpg')) ?> };</script>
  <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/main.js') ?>"></script>
  <?php if (isset($extra_js)) { echo $extra_js; } ?>
</body>
</html>
