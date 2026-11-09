<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LOKET (pembeli): melihat event yang masih dibuka dan memesan tiket.
 * Pesanan dibuat berstatus "menunggu" lalu dibayar di Kasir.
 */
class Loket extends MY_Controller {

	protected $roles = array('pembeli');

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('event_model', 'tiket_model', 'transaksi_model'));
	}

	public function index()
	{
		$events = $this->event_model->terbuka();
		$per_event = array();
		foreach ($this->tiket_model->tersedia_dipesan() as $t)
		{
			$per_event[$t->id_event][] = $t;
		}

		$this->render('loket/index', array(
			'title' => 'Loket Tiket', 'icon' => 'bi-shop', 'eyebrow' => 'Pembeli',
			'subtitle' => 'Pilih event dan jenis tiket, lalu pesan.',
			'actions' => '<a class="btn btn-outline-primary btn-sm" href="'.site_url('kasir').'"><i class="bi bi-cash-coin"></i> Ke Kasir</a>',
			'events' => $this->event_model->semua_lengkap_terbuka($events),
			'tikets' => $per_event,
		));
	}

	public function pesan($id_tiket = 0)
	{
		$tiket = NULL;
		foreach ($this->tiket_model->tersedia_dipesan() as $t)
		{
			if ((int) $t->id_tiket === (int) $id_tiket) { $tiket = $t; break; }
		}
		if ( ! $tiket)
		{
			$this->gagal('Tiket tidak tersedia (event sudah selesai atau tidak aktif).');
			redirect('loket');
		}

		if ($this->input->method() === 'post')
		{
			$this->form_validation->set_rules('jumlah', 'Jumlah tiket', 'trim|required|is_natural_no_zero');
			$this->form_validation->set_rules('total_bayar', 'Total pembayaran', 'trim|required|numeric');
			$this->form_validation->set_rules('catatan', 'Catatan', 'trim|max_length[255]');
			$this->form_validation->set_message('is_natural_no_zero', '{field} harus berupa angka bulat lebih dari nol.');

			if ($this->form_validation->run())
			{
				$hasil = $this->transaksi_model->buat(
					(int) $tiket->id_tiket,
					(int) $this->input->post('jumlah'),
					(int) $this->session->userdata('id_pemesan'),
					NULL,
					(float) $this->input->post('total_bayar'),
					'menunggu',
					(string) $this->input->post('catatan', TRUE)
				);
				if ($hasil['ok'])
				{
					$this->sukses('Pesanan dibuat. Silakan lanjut membayar di Kasir.');
					redirect('kasir/bayar/'.$hasil['id']);
				}
				$this->gagal_now($hasil['pesan']);
			}
		}

		$this->render('loket/pesan', array(
			'title' => 'Pesan Tiket', 'icon' => 'bi-shop', 'eyebrow' => 'Loket',
			'subtitle' => 'Isi jumlah tiket, total dihitung otomatis.',
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('loket').'"><i class="bi bi-arrow-left"></i> Kembali</a>',
			't' => $tiket,
		));
	}
}
