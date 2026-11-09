<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('laporan_model', 'event_model'));
	}

	public function index()
	{
		$id_event = (int) $this->input->get('id_event');
		$this->render('laporan/index', array(
			'title' => 'Rekap Tiket', 'icon' => 'bi-bar-chart-line', 'eyebrow' => 'Laporan',
			'subtitle' => 'Rekap tiket terjual, tiket tersisa, dan pendapatan per jenis tiket.',
			'actions' => '<button class="btn btn-light btn-sm" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>',
			'rows' => $this->laporan_model->rekap($id_event ?: NULL),
			'events' => $this->event_model->semua('nama_event'),
			'id_event' => $id_event,
		));
	}
}
