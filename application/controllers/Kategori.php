<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('kategori_model');
	}

	public function index()
	{
		$this->render('kategori/index', array(
			'title' => 'Kategori Event', 'icon' => 'bi-tags', 'eyebrow' => 'Data Master',
			'subtitle' => 'Kelompokkan event berdasarkan jenisnya.',
			'actions' => '<a class="btn btn-primary btn-sm" href="'.site_url('kategori/tambah').'"><i class="bi bi-plus-lg"></i> Tambah Kategori</a>',
			'rows' => $this->kategori_model->semua_dengan_jumlah(),
		));
	}

	private function rules($id = 0)
	{
		$this->form_validation->set_rules('nama_kategori', 'Nama kategori', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[255]');
	}

	private function nama_unik($nama, $id = 0)
	{
		$this->db->where('nama_kategori', $nama);
		if ($id) { $this->db->where('id_kategori !=', $id); }
		return $this->db->count_all_results('kategori') === 0;
	}

	public function tambah()
	{
		$this->rules();
		if ($this->form_validation->run())
		{
			$nama = $this->input->post('nama_kategori', TRUE);
			if ($this->nama_unik($nama))
			{
				$this->kategori_model->insert(array('nama_kategori' => $nama, 'keterangan' => $this->input->post('keterangan', TRUE)));
				$this->sukses('Kategori berhasil ditambahkan.');
				redirect('kategori');
			}
			$this->gagal_now('Nama kategori sudah ada.');
		}
		$this->form_view('Tambah Kategori', NULL);
	}

	public function ubah($id = 0)
	{
		$row = $this->kategori_model->find((int) $id) or show_404();
		$this->rules();
		if ($this->form_validation->run())
		{
			$nama = $this->input->post('nama_kategori', TRUE);
			if ($this->nama_unik($nama, $row->id_kategori))
			{
				$this->kategori_model->update($row->id_kategori, array('nama_kategori' => $nama, 'keterangan' => $this->input->post('keterangan', TRUE)));
				$this->sukses('Kategori berhasil diperbarui.');
				redirect('kategori');
			}
			$this->gagal_now('Nama kategori sudah ada.');
		}
		$this->form_view('Ubah Kategori', $row);
	}

	public function hapus($id = 0)
	{
		$this->wajib_post();
		$id = (int) $id;
		if ($this->kategori_model->dipakai($id))
		{
			$this->gagal('Kategori tidak dapat dihapus karena masih dipakai oleh event.');
		}
		else
		{
			$this->kategori_model->delete($id);
			$this->sukses('Kategori berhasil dihapus.');
		}
		redirect('kategori');
	}

	private function form_view($title, $row)
	{
		$this->render('kategori/form', array(
			'title' => $title, 'icon' => 'bi-tags', 'eyebrow' => 'Data Master', 'subtitle' => 'Isi data kategori event.',
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('kategori').'"><i class="bi bi-arrow-left"></i> Kembali</a>',
			'row' => $row,
		));
	}
}
