<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * KASIR (pembeli): melihat pesanan sendiri dan membayar pesanan yang menunggu.
 * Semua query dibatasi pada id_pemesan milik akun yang login.
 */
class Kasir extends MY_Controller {

	protected $roles = array('pembeli');

	public function __construct()
	{
		parent::__construct();
		$this->load->model('transaksi_model');
	}

	private function id_pemesan()
	{
		return (int) $this->session->userdata('id_pemesan');
	}

	/** Ambil transaksi milik sendiri atau 404 */
	private function milik_sendiri($id)
	{
		$trx = $this->transaksi_model->detail((int) $id);
		if ( ! $trx OR (int) $trx->id_pemesan !== $this->id_pemesan())
		{
			show_404();
		}
		return $trx;
	}

	public function index()
	{
		$f = array('id_pemesan' => $this->id_pemesan());
		$this->render('kasir/index', array(
			'title' => 'Kasir', 'icon' => 'bi-cash-coin', 'eyebrow' => 'Pembeli',
			'subtitle' => 'Pesanan Anda dan pembayarannya.',
			'actions' => '<a class="btn btn-primary btn-sm" href="'.site_url('loket').'"><i class="bi bi-shop"></i> Ke Loket</a>',
			'rows' => $this->transaksi_model->cari($f, 100, 0),
		));
	}

	public function bayar($id = 0)
	{
		$trx = $this->milik_sendiri($id);
		if ($trx->status !== 'menunggu')
		{
			redirect('kasir/detail/'.$trx->id_transaksi);
		}

		if ($this->input->method() === 'post')
		{
			$this->form_validation->set_rules('metode', 'Metode pembayaran', 'required|in_list[Transfer Bank,QRIS,E-Wallet,Tunai]');
			// Uang yang dibayarkan hanya diisi untuk metode Tunai; metode lain otomatis sebesar total.
			$tunai = ($this->input->post('metode') === 'Tunai');
			if ($tunai)
			{
				$this->form_validation->set_rules('jumlah_bayar', 'Uang diterima', 'trim|required|numeric|greater_than[0]');
				$this->form_validation->set_message('greater_than', '{field} harus lebih dari 0.');
			}

			if ($this->form_validation->run())
			{
				$bukti = NULL;
				if ( ! $tunai)
				{
					$bukti = $this->simpan_bukti();
					if ($bukti === FALSE)
					{
						return $this->tampil_bayar($trx);
					}
				}
				$hasil = $this->transaksi_model->bayar($trx->id_transaksi, $this->id_pemesan(), $this->input->post('metode'), $tunai ? (float) $this->input->post('jumlah_bayar') : (float) $trx->total_harga, $bukti);
				if ($hasil['ok'])
				{
					$this->sukses($hasil['pesan']);
					redirect('kasir/detail/'.$trx->id_transaksi);
				}
				if ($bukti) { @unlink(APPPATH.'uploads_bukti/'.$bukti); }
				$this->gagal_now($hasil['pesan']);
			}
		}

		$this->tampil_bayar($trx);
	}

	/** Simpan unggahan bukti pembayaran. Mengembalikan nama file, atau FALSE bila gagal. */
	private function simpan_bukti()
	{
		$this->load->library('upload', array(
			'upload_path'   => APPPATH.'uploads_bukti/',
			'allowed_types' => 'jpg|jpeg|png|pdf',
			'max_size'      => 2048,
			'encrypt_name'  => TRUE,
		));
		if ( ! $this->upload->do_upload('bukti'))
		{
			$err = strip_tags($this->upload->display_errors('', ''));
			$this->gagal_now(strpos($err, 'did not select') !== FALSE
				? 'Bukti pembayaran wajib diunggah untuk metode non-tunai.'
				: 'Bukti pembayaran: '.$err);
			return FALSE;
		}
		$d = $this->upload->data();
		return $d['file_name'];
	}

	/** Bukti pembayaran milik sendiri */
	public function bukti($id = 0)
	{
		$trx = $this->milik_sendiri($id);
		$this->kirim_bukti($trx->bukti_bayar);
	}

	private function tampil_bayar($trx)
	{
		$this->render('kasir/bayar', array(
			'title' => 'Pembayaran', 'icon' => 'bi-cash-coin', 'eyebrow' => 'Kasir',
			'subtitle' => 'Selesaikan pembayaran untuk '.$trx->kode_transaksi.'.',
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('kasir').'"><i class="bi bi-arrow-left"></i> Kembali</a>',
			'trx' => $trx,
		));
	}

	public function detail($id = 0)
	{
		$trx = $this->milik_sendiri($id);
		$this->render('kasir/detail', array(
			'title' => 'Bukti Pesanan', 'icon' => 'bi-receipt', 'eyebrow' => 'Kasir',
			'subtitle' => $trx->kode_transaksi,
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('kasir').'"><i class="bi bi-arrow-left"></i> Kembali</a> '
				.'<button class="btn btn-light btn-sm" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>',
			'trx' => $trx,
		));
	}

	/** Pembeli hanya boleh membatalkan pesanan sendiri yang belum dibayar */
	public function batal($id = 0)
	{
		$this->wajib_post();
		$trx = $this->milik_sendiri($id);
		if ($trx->status !== 'menunggu')
		{
			$this->gagal('Hanya pesanan yang belum dibayar yang dapat dibatalkan.');
		}
		else
		{
			$h = $this->transaksi_model->ubah_status($trx->id_transaksi, 'batal');
			$h['ok'] ? $this->sukses('Pesanan '.$trx->kode_transaksi.' dibatalkan.') : $this->gagal($h['pesan']);
		}
		redirect('kasir');
	}
}
