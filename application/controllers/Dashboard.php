<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('laporan_model', 'transaksi_model'));
	}

	public function index()
	{
		$this->render('dashboard/index', array(
			'title'      => 'Dashboard',
			'icon'       => 'bi-speedometer2',
			'eyebrow'    => 'Ringkasan',
			'subtitle'   => 'Pantau event, penjualan tiket, dan transaksi terbaru.',
			'actions'    => '<a class="btn btn-primary btn-sm" href="'.site_url('transaksi/tambah').'"><i class="bi bi-cart-plus"></i> Pesan Tiket</a>',
			'ringkasan'  => $this->laporan_model->ringkasan(),
			'mendatang'  => $this->laporan_model->event_mendatang(5),
			'terbaru'    => $this->transaksi_model->terbaru(6),
		));
	}
}
