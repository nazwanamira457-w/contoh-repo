<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi extends MY_Controller {

	private $per_page = 10;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('transaksi_model', 'tiket_model', 'pemesan_model', 'event_model'));
	}

	public function bukti($id = 0)
	{
		$trx = $this->transaksi_model->detail((int) $id);
		if ( ! $trx) { show_404(); }
		$this->kirim_bukti($trx->bukti_bayar);
	}

	public function index()
	{
		$f = array(
			'q'        => trim((string) $this->input->get('q', TRUE)),
			'status'   => in_array($this->input->get('status'), array('menunggu', 'lunas', 'batal'), TRUE) ? $this->input->get('status') : '',
			'id_event' => (int) $this->input->get('id_event'),
		);
		$total = $this->transaksi_model->hitung($f);
		$hal = max(1, (int) $this->input->get('hal'));
		$offset = ($hal - 1) * $this->per_page;

		$this->render('transaksi/index', array(
			'title' => 'Transaksi Tiket', 'icon' => 'bi-receipt', 'eyebrow' => 'Pemesanan',
			'subtitle' => 'Cari dan pantau seluruh transaksi pemesanan tiket.',
			'actions' => '<a class="btn btn-primary btn-sm" href="'.site_url('transaksi/tambah').'"><i class="bi bi-cart-plus"></i> Pesan Tiket</a>',
			'rows' => $this->transaksi_model->cari($f, $this->per_page, $offset),
			'total' => $total,
			'f' => $f,
			'events' => $this->event_model->semua('nama_event'),
			'links' => $this->paginasi(site_url('transaksi'), $total, $this->per_page),
			'offset' => $offset,
		));
	}

	public function detail($id = 0)
	{
		$trx = $this->transaksi_model->detail((int) $id) or show_404();
		$this->render('transaksi/detail', array(
			'title' => 'Transaksi '.$trx->kode_transaksi, 'icon' => 'bi-receipt', 'eyebrow' => 'Detail Pemesanan',
			'subtitle' => 'Rincian pemesanan dan status pembayaran.',
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('transaksi').'"><i class="bi bi-arrow-left"></i> Kembali</a> '
				.'<button class="btn btn-light btn-sm" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>',
			'trx' => $trx,
		));
	}

	public function tambah()
	{
		$tikets = $this->tiket_model->tersedia_dipesan();

		if ($this->input->method() === 'post')
		{
			$baru = $this->input->post('mode_pemesan') !== 'lama';

			$this->form_validation->set_rules('id_tiket', 'Jenis tiket', 'required|is_natural_no_zero');
			$this->form_validation->set_rules('jumlah', 'Jumlah tiket', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('total_bayar', 'Total pembayaran', 'trim|required|numeric');
			$this->form_validation->set_rules('status', 'Status', 'required|in_list[menunggu,lunas]');
			$this->form_validation->set_rules('catatan', 'Catatan', 'trim|max_length[255]');
			$this->form_validation->set_message('is_natural_no_zero', '{field} harus berupa angka bulat lebih dari nol.');

			if ($baru)
			{
				$this->form_validation->set_rules('nama', 'Nama pemesan', 'trim|required|max_length[100]');
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[100]');
				$this->form_validation->set_rules('no_hp', 'No. HP', 'trim|required|regex_match[/^\+?[0-9]{8,15}$/]');
				$this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[255]');
			}
			else
			{
				$this->form_validation->set_rules('id_pemesan', 'Pemesan', 'required|is_natural_no_zero');
			}

			if ($this->form_validation->run())
			{
				$pemesan_baru = $baru ? array(
					'nama'   => $this->input->post('nama', TRUE),
					'email'  => $this->input->post('email', TRUE),
					'no_hp'  => $this->input->post('no_hp', TRUE),
					'alamat' => $this->input->post('alamat', TRUE),
				) : NULL;

				$hasil = $this->transaksi_model->buat(
					(int) $this->input->post('id_tiket'),
					(int) $this->input->post('jumlah'),
					$baru ? NULL : (int) $this->input->post('id_pemesan'),
					$pemesan_baru,
					(float) $this->input->post('total_bayar'),
					$this->input->post('status'),
					(string) $this->input->post('catatan', TRUE)
				);

				if ($hasil['ok'])
				{
					$this->sukses($hasil['pesan']);
					redirect('transaksi/detail/'.$hasil['id']);
				}
				$this->gagal_now($hasil['pesan']);
			}
		}

		$this->render('transaksi/form', array(
			'title' => 'Pesan Tiket', 'icon' => 'bi-cart-plus', 'eyebrow' => 'Pemesanan',
			'subtitle' => 'Pilih tiket, isi data pemesan, total dihitung otomatis.',
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('transaksi').'"><i class="bi bi-arrow-left"></i> Kembali</a>',
			'tikets' => $tikets,
			'pemesan' => $this->pemesan_model->semua('nama'),
			'pre_tiket' => (int) $this->input->get('id_tiket'),
		));
	}

	public function status($id = 0, $baru = '')
	{
		$this->wajib_post();
		$hasil = $this->transaksi_model->ubah_status((int) $id, $baru);
		$hasil['ok'] ? $this->sukses($hasil['pesan']) : $this->gagal($hasil['pesan']);
		redirect($this->input->post('kembali') === 'list' ? 'transaksi' : 'transaksi/detail/'.(int) $id);
	}
}
