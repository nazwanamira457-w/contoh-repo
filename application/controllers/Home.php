<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/** Landing page publik: daftar event + tiket. Pemesanan memerlukan akun pembeli. */
class Home extends MY_Controller {

	protected $public = TRUE;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('event_model', 'tiket_model'));
	}

	public function index()
	{
		$per_event = array();
		foreach ($this->tiket_model->tersedia_dipesan() as $t)
		{
			$per_event[$t->id_event][] = $t;
		}

		$events = array();
		$kategori = array();
		foreach ($this->event_model->semua_lengkap_terbuka($this->event_model->terbuka()) as $e)
		{
			$e->tikets = isset($per_event[$e->id_event]) ? $per_event[$e->id_event] : array();
			$harga = array();
			$sisa = 0;
			foreach ($e->tikets as $t)
			{
				$sisa += $t->sisa;
				if ($t->sisa > 0) { $harga[] = (float) $t->harga; }
			}
			$e->sisa_total = $sisa;
			$e->harga_min = $harga ? min($harga) : NULL;
			$events[] = $e;
			$kategori[$e->nama_kategori] = TRUE;
		}

		// Event unggulan di hero: yang paling dekat akan dimulai dan masih punya tiket
		$unggulan = NULL;
		$hari_ini = date('Y-m-d');
		foreach ($events as $e)
		{
			if ($e->sisa_total > 0 && $e->tanggal_mulai >= $hari_ini) { $unggulan = $e; break; }
		}
		if ($unggulan === NULL)
		{
			foreach ($events as $e) { if ($e->sisa_total > 0) { $unggulan = $e; break; } }
		}

		$this->load->view('home/index', array(
			'events'   => $events,
			'kategori' => array_keys($kategori),
			'unggulan' => $unggulan,
			'role'     => $this->session->userdata('role'),
			'nama'     => $this->session->userdata('nama'),
		));
	}
}
