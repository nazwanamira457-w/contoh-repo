<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pemesan extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pemesan_model');
	}

	public function index()
	{
		$this->render('pemesan/index', array(
			'title' => 'Data Pemesan', 'icon' => 'bi-people', 'eyebrow' => 'Pengunjung',
			'subtitle' => 'Daftar pengunjung / pemesan tiket.',
			'actions' => '<a class="btn btn-primary btn-sm" href="'.site_url('pemesan/tambah').'"><i class="bi bi-person-plus"></i> Tambah Pemesan</a>',
			'rows' => $this->pemesan_model->semua_dengan_jumlah(),
		));
	}

	private function rules()
	{
		$this->form_validation->set_rules('nama', 'Nama', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[100]');
		$this->form_validation->set_rules('no_hp', 'No. HP', 'trim|required|regex_match[/^\+?[0-9]{8,15}$/]');
		$this->form_validation->set_rules('alamat', 'Alamat', 'trim|max_length[255]');
		$this->form_validation->set_message('regex_match', '{field} harus 8-15 digit angka (boleh diawali +).');
	}

	private function data()
	{
		return array(
			'nama'   => $this->input->post('nama', TRUE),
			'email'  => $this->input->post('email', TRUE),
			'no_hp'  => $this->input->post('no_hp', TRUE),
			'alamat' => $this->input->post('alamat', TRUE),
		);
	}

	public function tambah()
	{
		$this->rules();
		if ($this->form_validation->run())
		{
			$this->pemesan_model->insert($this->data());
			$this->sukses('Pemesan berhasil ditambahkan.');
			redirect('pemesan');
		}
		$this->form_view('Tambah Pemesan', NULL);
	}

	public function ubah($id = 0)
	{
		$row = $this->pemesan_model->find((int) $id) or show_404();
		$this->rules();
		if ($this->form_validation->run())
		{
			$this->pemesan_model->update($row->id_pemesan, $this->data());
			$this->sukses('Data pemesan berhasil diperbarui.');
			redirect('pemesan');
		}
		$this->form_view('Ubah Pemesan', $row);
	}

	public function hapus($id = 0)
	{
		$this->wajib_post();
		$id = (int) $id;
		if ($this->pemesan_model->dipakai($id))
		{
			$this->gagal('Pemesan tidak dapat dihapus karena sudah memiliki transaksi atau terhubung dengan akun pembeli.');
		}
		else
		{
			$this->pemesan_model->delete($id);
			$this->sukses('Pemesan berhasil dihapus.');
		}
		redirect('pemesan');
	}

	private function form_view($title, $row)
	{
		$this->render('pemesan/form', array(
			'title' => $title, 'icon' => 'bi-people', 'eyebrow' => 'Pengunjung', 'subtitle' => 'Isi data pemesan dengan lengkap.',
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('pemesan').'"><i class="bi bi-arrow-left"></i> Kembali</a>',
			'row' => $row,
		));
	}
}
