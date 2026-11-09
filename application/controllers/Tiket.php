<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tiket extends MY_Controller {

	private $row_edit = NULL;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('tiket_model', 'event_model'));
	}

	public function index()
	{
		$this->render('tiket/index', array(
			'title' => 'Jenis Tiket', 'icon' => 'bi-ticket-perforated', 'eyebrow' => 'Manajemen',
			'subtitle' => 'Atur jenis tiket, harga, dan kuota untuk setiap event.',
			'actions' => '<a class="btn btn-primary btn-sm" href="'.site_url('tiket/tambah').'"><i class="bi bi-plus-lg"></i> Tambah Tiket</a>',
			'rows' => $this->tiket_model->semua_lengkap(),
		));
	}

	private function rules()
	{
		$this->form_validation->set_rules('id_event', 'Event', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('nama_tiket', 'Nama tiket', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('harga', 'Harga tiket', 'trim|required|numeric|greater_than_equal_to[0]|less_than_equal_to[999999999]');
		$this->form_validation->set_rules('kuota', 'Kuota tiket', 'trim|required|is_natural_no_zero|less_than_equal_to[1000000]');
		$this->form_validation->set_message('is_natural_no_zero', '{field} harus berupa angka bulat positif (lebih dari 0).');
	}

	private function data()
	{
		return array(
			'id_event'   => (int) $this->input->post('id_event'),
			'nama_tiket' => $this->input->post('nama_tiket', TRUE),
			'harga'      => (float) $this->input->post('harga'),
			'kuota'      => (int) $this->input->post('kuota'),
		);
	}

	public function tambah()
	{
		$this->rules();
		if ($this->form_validation->run())
		{
			if ( ! $this->event_model->find((int) $this->input->post('id_event')))
			{
				$this->gagal_now('Event yang dipilih tidak ditemukan.');
			}
			else
			{
				$d = $this->data();
				$this->tiket_model->insert($d);
				$this->sukses('Jenis tiket berhasil ditambahkan.');
				redirect('event/detail/'.$d['id_event']);
			}
		}
		$this->form_view('Tambah Jenis Tiket', NULL);
	}

	public function ubah($id = 0)
	{
		$row = $this->tiket_model->find((int) $id) or show_404();
		$this->rules();
		if ($this->form_validation->run())
		{
			$d = $this->data();
			if ( ! $this->event_model->find($d['id_event']))
			{
				$this->gagal_now('Event yang dipilih tidak ditemukan.');
			}
			elseif ($d['kuota'] < (int) $row->terjual)
			{
				$this->gagal_now('Kuota tidak boleh lebih kecil dari tiket yang sudah terjual/dipesan ('.$row->terjual.').');
			}
			elseif ($d['id_event'] != $row->id_event && $this->tiket_model->dipakai($row->id_tiket))
			{
				$this->gagal_now('Event tiket tidak dapat dipindah karena sudah ada transaksi.');
			}
			else
			{
				$this->tiket_model->update($row->id_tiket, $d);
				$this->sukses('Jenis tiket berhasil diperbarui.');
				redirect('event/detail/'.$d['id_event']);
			}
		}
		$this->form_view('Ubah Jenis Tiket', $row);
	}

	public function hapus($id = 0)
	{
		$this->wajib_post();
		$row = $this->tiket_model->find((int) $id) or show_404();
		if ($this->tiket_model->dipakai($row->id_tiket))
		{
			$this->gagal('Tiket tidak dapat dihapus karena sudah memiliki transaksi.');
		}
		else
		{
			$this->tiket_model->delete($row->id_tiket);
			$this->sukses('Jenis tiket berhasil dihapus.');
		}
		redirect('event/detail/'.$row->id_event);
	}

	private function form_view($title, $row)
	{
		$this->render('tiket/form', array(
			'title' => $title, 'icon' => 'bi-ticket-perforated', 'eyebrow' => 'Manajemen', 'subtitle' => 'Tentukan harga dan kuota tiket.',
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('tiket').'"><i class="bi bi-arrow-left"></i> Kembali</a>',
			'row' => $row,
			'events' => $this->event_model->semua('tanggal_mulai DESC'),
			'pre_event' => (int) $this->input->get('id_event'),
		));
	}
}
