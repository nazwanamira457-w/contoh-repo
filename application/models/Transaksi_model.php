<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi_model extends MY_Model {
	protected $table = 'transaksi';
	protected $pk = 'id_transaksi';

	private function base_query()
	{
		$this->db->from('transaksi t')
			->join('pemesan p', 'p.id_pemesan = t.id_pemesan')
			->join('jenis_tiket jt', 'jt.id_tiket = t.id_tiket')
			->join('event e', 'e.id_event = jt.id_event');
	}

	private function terapkan_filter($f)
	{
		if ( ! empty($f['q']))
		{
			$this->db->group_start()
				->like('t.kode_transaksi', $f['q'])
				->or_like('p.nama', $f['q'])
				->or_like('p.email', $f['q'])
				->or_like('p.no_hp', $f['q'])
				->or_like('e.nama_event', $f['q'])
				->or_like('jt.nama_tiket', $f['q'])
				->group_end();
		}
		if ( ! empty($f['status']))
		{
			$this->db->where('t.status', $f['status']);
		}
		if ( ! empty($f['id_pemesan']))
		{
			$this->db->where('t.id_pemesan', (int) $f['id_pemesan']);
		}
		if ( ! empty($f['id_event']))
		{
			$this->db->where('e.id_event', (int) $f['id_event']);
		}
	}

	public function hitung($f)
	{
		$this->base_query();
		$this->terapkan_filter($f);
		return $this->db->count_all_results();
	}

	public function cari($f, $limit, $offset)
	{
		$this->db->select('t.*, p.nama AS nama_pemesan, p.email, p.no_hp, jt.nama_tiket, e.id_event, e.nama_event');
		$this->base_query();
		$this->terapkan_filter($f);
		return $this->db->order_by('t.tanggal_pesan', 'DESC')->order_by('t.id_transaksi', 'DESC')
			->limit($limit, $offset)->get()->result();
	}

	public function detail($id)
	{
		return $this->db->select('t.*, p.nama AS nama_pemesan, p.email, p.no_hp, p.alamat,
				jt.nama_tiket, e.nama_event, e.tanggal_mulai, e.tanggal_selesai, e.jam_mulai, l.nama_lokasi, l.alamat AS alamat_lokasi, l.kota')
			->from('transaksi t')
			->join('pemesan p', 'p.id_pemesan = t.id_pemesan')
			->join('jenis_tiket jt', 'jt.id_tiket = t.id_tiket')
			->join('event e', 'e.id_event = jt.id_event')
			->join('lokasi l', 'l.id_lokasi = e.id_lokasi')
			->where('t.id_transaksi', $id)->get()->row();
	}

	public function terbaru($n = 6)
	{
		$this->db->select('t.*, p.nama AS nama_pemesan, jt.nama_tiket, e.nama_event');
		$this->base_query();
		return $this->db->order_by('t.tanggal_pesan', 'DESC')->order_by('t.id_transaksi', 'DESC')->limit($n)->get()->result();
	}

	private function buat_kode()
	{
		do
		{
			$kode = 'TRX-'.date('ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
		}
		while ($this->db->where('kode_transaksi', $kode)->count_all_results('transaksi') > 0);
		return $kode;
	}

	/**
	 * Membuat pemesanan tiket secara atomik.
	 * Baris tiket dikunci (SELECT ... FOR UPDATE) sehingga dua pemesanan
	 * bersamaan tidak bisa sama-sama melewati kuota.
	 *
	 * @param int        $id_tiket
	 * @param int        $jumlah
	 * @param int|null   $id_pemesan  gunakan pemesan yang sudah ada, atau NULL
	 * @param array|null $pemesan_baru data pemesan baru (nama, email, no_hp, alamat)
	 * @param float      $total_dikirim total pembayaran dari form (untuk dicocokkan)
	 * @return array  array('ok'=>bool, 'pesan'=>string, 'id'=>int)
	 */
	public function buat($id_tiket, $jumlah, $id_pemesan, $pemesan_baru, $total_dikirim, $status, $catatan)
	{
		$this->db->trans_begin();

		$tiket = $this->db->query(
			'SELECT jt.*, e.nama_event, e.status AS status_event, e.tanggal_selesai
			 FROM jenis_tiket jt JOIN event e ON e.id_event = jt.id_event
			 WHERE jt.id_tiket = ? FOR UPDATE', array((int) $id_tiket))->row();

		$gagal = NULL;
		if ( ! $tiket)
		{
			$gagal = 'Jenis tiket tidak ditemukan.';
		}
		elseif ($tiket->status_event !== 'aktif')
		{
			$gagal = 'Event "'.$tiket->nama_event.'" tidak aktif, pemesanan ditolak.';
		}
		elseif (event_selesai($tiket->tanggal_selesai))
		{
			$gagal = 'Event "'.$tiket->nama_event.'" sudah selesai, tidak dapat menerima pemesanan.';
		}
		elseif ($jumlah <= 0)
		{
			$gagal = 'Jumlah tiket harus lebih dari nol.';
		}
		else
		{
			$sisa = (int) $tiket->kuota - (int) $tiket->terjual;
			if ($jumlah > $sisa)
			{
				$gagal = ($sisa > 0)
					? 'Kuota tidak mencukupi. Sisa tiket "'.$tiket->nama_tiket.'" hanya '.$sisa.', sedangkan Anda memesan '.$jumlah.'.'
					: 'Tiket "'.$tiket->nama_tiket.'" sudah habis terjual.';
			}
		}

		if ($gagal === NULL)
		{
			$total = round((float) $tiket->harga * $jumlah, 2);
			if (abs($total - (float) $total_dikirim) > 0.001)
			{
				$gagal = 'Total pembayaran tidak sesuai. Seharusnya '.rupiah($total).' ('.$jumlah.' x '.rupiah($tiket->harga).').';
			}
		}

		if ($gagal !== NULL)
		{
			$this->db->trans_rollback();
			return array('ok' => FALSE, 'pesan' => $gagal);
		}

		if ($id_pemesan)
		{
			if ( ! $this->db->where('id_pemesan', $id_pemesan)->count_all_results('pemesan'))
			{
				$this->db->trans_rollback();
				return array('ok' => FALSE, 'pesan' => 'Data pemesan tidak ditemukan.');
			}
		}
		else
		{
			$this->db->insert('pemesan', $pemesan_baru);
			$id_pemesan = $this->db->insert_id();
		}

		$this->db->insert('transaksi', array(
			'kode_transaksi' => $this->buat_kode(),
			'id_pemesan'     => $id_pemesan,
			'id_tiket'       => $tiket->id_tiket,
			'jumlah'         => $jumlah,
			'harga_satuan'   => $tiket->harga,
			'total_harga'    => $total,
			'status'         => $status,
			'catatan'        => $catatan !== '' ? $catatan : NULL,
			'tanggal_pesan'  => date('Y-m-d H:i:s'),
			'tanggal_bayar'  => ($status === 'lunas') ? date('Y-m-d H:i:s') : NULL,
		));
		$id = $this->db->insert_id();

		// Kurangi kuota (tambah terjual)
		$this->db->set('terjual', 'terjual + '.(int) $jumlah, FALSE)
			->where('id_tiket', $tiket->id_tiket)->update('jenis_tiket');

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return array('ok' => FALSE, 'pesan' => 'Terjadi kesalahan saat menyimpan transaksi.');
		}
		$this->db->trans_commit();
		return array('ok' => TRUE, 'pesan' => 'Pemesanan berhasil disimpan.', 'id' => $id);
	}

	/**
	 * Pembayaran oleh pembeli (Kasir). Hanya pesanan milik sendiri berstatus menunggu.
	 * Jumlah dibayar harus sama persis dengan total harga.
	 */
	public function bayar($id, $id_pemesan, $metode, $jumlah_bayar, $bukti = NULL)
	{
		$this->db->trans_begin();
		$trx = $this->db->query('SELECT t.*, e.tanggal_selesai, e.status AS status_event FROM transaksi t
			JOIN jenis_tiket jt ON jt.id_tiket = t.id_tiket JOIN event e ON e.id_event = jt.id_event
			WHERE t.id_transaksi = ? FOR UPDATE', array((int) $id))->row();

		$gagal = NULL;
		if ( ! $trx OR (int) $trx->id_pemesan !== (int) $id_pemesan)
		{
			$gagal = 'Transaksi tidak ditemukan.';
		}
		elseif ($trx->status !== 'menunggu')
		{
			$gagal = 'Transaksi ini berstatus "'.$trx->status.'" dan tidak dapat dibayar.';
		}
		elseif (event_selesai($trx->tanggal_selesai))
		{
			$gagal = 'Event sudah selesai, pesanan tidak dapat dibayar. Silakan batalkan pesanan.';
		}
		elseif ($metode === 'Tunai' && (float) $jumlah_bayar + 0.001 < (float) $trx->total_harga)
		{
			$gagal = 'Uang yang diterima kurang. Total yang harus dibayar '.rupiah($trx->total_harga).'.';
		}

		if ($gagal !== NULL)
		{
			$this->db->trans_rollback();
			return array('ok' => FALSE, 'pesan' => $gagal);
		}

		// Non-tunai dibayar pas sebesar total; tunai boleh lebih, selisihnya menjadi kembalian.
		$dibayar   = ($metode === 'Tunai') ? (float) $jumlah_bayar : (float) $trx->total_harga;
		$kembalian = max(0, $dibayar - (float) $trx->total_harga);
		$this->db->where('id_transaksi', $trx->id_transaksi)->update('transaksi', array(
			'status' => 'lunas', 'metode_bayar' => $metode, 'tanggal_bayar' => date('Y-m-d H:i:s'),
			'jumlah_bayar' => $dibayar, 'kembalian' => $kembalian, 'bukti_bayar' => $bukti,
		));
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return array('ok' => FALSE, 'pesan' => 'Gagal memproses pembayaran.');
		}
		$this->db->trans_commit();
		$pesan = 'Pembayaran berhasil. Pesanan '.$trx->kode_transaksi.' lunas.';
		if ($kembalian > 0) { $pesan .= ' Kembalian Anda '.rupiah($kembalian).'.'; }
		return array('ok' => TRUE, 'pesan' => $pesan, 'id' => $trx->id_transaksi);
	}

	/**
	 * Ubah status: menunggu -> lunas | batal ; lunas -> batal.
	 * Pembatalan mengembalikan kuota.
	 */
	public function ubah_status($id, $baru)
	{
		$this->db->trans_begin();

		$trx = $this->db->query('SELECT * FROM transaksi WHERE id_transaksi = ? FOR UPDATE', array((int) $id))->row();
		if ( ! $trx)
		{
			$this->db->trans_rollback();
			return array('ok' => FALSE, 'pesan' => 'Transaksi tidak ditemukan.');
		}

		$boleh = array('menunggu' => array('lunas', 'batal'), 'lunas' => array('batal'), 'batal' => array());
		if ( ! in_array($baru, $boleh[$trx->status], TRUE))
		{
			$this->db->trans_rollback();
			return array('ok' => FALSE, 'pesan' => 'Status "'.$trx->status.'" tidak dapat diubah menjadi "'.$baru.'".');
		}

		$set = array('status' => $baru);
		if ($baru === 'lunas')
		{
			$set['tanggal_bayar'] = date('Y-m-d H:i:s');
		}
		$this->db->where('id_transaksi', $id)->update('transaksi', $set);

		if ($baru === 'batal')
		{
			// kunci baris tiket lalu kembalikan kuota
			$this->db->query('SELECT id_tiket FROM jenis_tiket WHERE id_tiket = ? FOR UPDATE', array($trx->id_tiket));
			$this->db->set('terjual', 'GREATEST(terjual - '.(int) $trx->jumlah.', 0)', FALSE)
				->where('id_tiket', $trx->id_tiket)->update('jenis_tiket');
		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return array('ok' => FALSE, 'pesan' => 'Gagal mengubah status.');
		}
		$this->db->trans_commit();
		return array('ok' => TRUE, 'pesan' => 'Status transaksi '.$trx->kode_transaksi.' diubah menjadi '.$baru.'.');
	}
}
