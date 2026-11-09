<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lokasi extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('lokasi_model');
	}

	public function index()
	{
		$this->render('lokasi/index', array(
			'title' => 'Lokasi', 'icon' => 'bi-geo-alt', 'eyebrow' => 'Data Master',
			'subtitle' => 'Tempat berlangsungnya wisata atau event.',
			'actions' => '<a class="btn btn-primary btn-sm" href="'.site_url('lokasi/tambah').'"><i class="bi bi-plus-lg"></i> Tambah Lokasi</a>',
			'rows' => $this->lokasi_model->semua_dengan_jumlah(),
		));
	}

	private function rules()
	{
		$this->form_validation->set_rules('nama_lokasi', 'Nama lokasi', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('kota', 'Kota', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[255]');
		$this->form_validation->set_rules('kapasitas', 'Kapasitas', 'trim|is_natural_no_zero');
	}

	private function data()
	{
		$kap = $this->input->post('kapasitas');
		return array(
			'nama_lokasi' => $this->input->post('nama_lokasi', TRUE),
			'kota'        => $this->input->post('kota', TRUE),
			'alamat'      => $this->input->post('alamat', TRUE),
			'kapasitas'   => ($kap === '' || $kap === NULL) ? NULL : (int) $kap,
		);
	}

	public function tambah()
	{
		$this->rules();
		if ($this->form_validation->run())
		{
			$this->lokasi_model->insert($this->data());
			$this->sukses('Lokasi berhasil ditambahkan.');
			redirect('lokasi');
		}
		$this->form_view('Tambah Lokasi', NULL);
	}

	public function ubah($id = 0)
	{
		$row = $this->lokasi_model->find((int) $id) or show_404();
		$this->rules();
		if ($this->form_validation->run())
		{
			$this->lokasi_model->update($row->id_lokasi, $this->data());
			$this->sukses('Lokasi berhasil diperbarui.');
			redirect('lokasi');
		}
		$this->form_view('Ubah Lokasi', $row);
	}

	public function hapus($id = 0)
	{
		$this->wajib_post();
		$id = (int) $id;
		if ($this->lokasi_model->dipakai($id))
		{
			$this->gagal('Lokasi tidak dapat dihapus karena masih dipakai oleh event.');
		}
		else
		{
			$this->lokasi_model->delete($id);
			$this->sukses('Lokasi berhasil dihapus.');
		}
		redirect('lokasi');
	}

	private function form_view($title, $row)
	{
		$this->render('lokasi/form', array(
			'title' => $title, 'icon' => 'bi-geo-alt', 'eyebrow' => 'Data Master', 'subtitle' => 'Isi data lokasi.',
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('lokasi').'"><i class="bi bi-arrow-left"></i> Kembali</a>',
			'row' => $row,
		));
	}
}
