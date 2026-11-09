<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('event_model', 'kategori_model', 'lokasi_model', 'tiket_model'));
	}

	public function index()
	{
		$this->render('event/index', array(
			'title' => 'Wisata / Event', 'icon' => 'bi-calendar-event', 'eyebrow' => 'Manajemen',
			'subtitle' => 'Kelola data wisata atau event beserta tanggal dan lokasinya.',
			'actions' => '<a class="btn btn-primary btn-sm" href="'.site_url('event/tambah').'"><i class="bi bi-plus-lg"></i> Tambah Event</a>',
			'rows' => $this->event_model->semua_lengkap(),
		));
	}

	public function detail($id = 0)
	{
		$ev = $this->event_model->detail((int) $id) or show_404();
		$this->render('event/detail', array(
			'title' => $ev->nama_event, 'icon' => 'bi-calendar-event', 'eyebrow' => 'Detail Event',
			'subtitle' => 'Informasi event dan jenis tiketnya.',
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('event').'"><i class="bi bi-arrow-left"></i> Kembali</a> '
				.'<a class="btn btn-primary btn-sm" href="'.site_url('tiket/tambah?id_event='.$ev->id_event).'"><i class="bi bi-ticket-perforated"></i> Tambah Tiket</a>',
			'ev' => $ev,
			'tikets' => $this->tiket_model->semua_lengkap($ev->id_event),
		));
	}

	private function rules()
	{
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim');
		$this->form_validation->set_rules('nama_event', 'Nama event', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('id_kategori', 'Kategori', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('id_lokasi', 'Lokasi', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('tanggal_mulai', 'Tanggal mulai', 'trim|required|valid_date');
		$this->form_validation->set_rules('tanggal_selesai', 'Tanggal selesai', 'trim|required|valid_date|tgl_tidak_sebelum[tanggal_mulai]');
		$this->form_validation->set_rules('jam_mulai', 'Jam mulai', 'trim|regex_match[/^([01][0-9]|2[0-3]):[0-5][0-9]$/]');
		$this->form_validation->set_rules('status', 'Status', 'required|in_list[aktif,nonaktif]');
	}

	private function data()
	{
		$jam = $this->input->post('jam_mulai');
		return array(
			'nama_event'      => $this->input->post('nama_event', TRUE),
			'id_kategori'     => (int) $this->input->post('id_kategori'),
			'id_lokasi'       => (int) $this->input->post('id_lokasi'),
			'tanggal_mulai'   => $this->input->post('tanggal_mulai'),
			'tanggal_selesai' => $this->input->post('tanggal_selesai'),
			'jam_mulai'       => $jam ? $jam.':00' : NULL,
			'deskripsi'       => $this->input->post('deskripsi', TRUE),
			'status'          => $this->input->post('status'),
		);
	}

	/** Pastikan kategori & lokasi benar-benar ada */
	private function relasi_ada()
	{
		if ( ! $this->kategori_model->find((int) $this->input->post('id_kategori')))
		{
			$this->gagal_now('Kategori yang dipilih tidak ditemukan.');
			return FALSE;
		}
		if ( ! $this->lokasi_model->find((int) $this->input->post('id_lokasi')))
		{
			$this->gagal_now('Lokasi yang dipilih tidak ditemukan.');
			return FALSE;
		}
		return TRUE;
	}

	public function tambah()
	{
		$this->rules();
		if ($this->form_validation->run() && $this->relasi_ada())
		{
			$id = $this->event_model->insert($this->data());
			$this->sukses('Event berhasil ditambahkan. Silakan buat jenis tiketnya.');
			redirect('event/detail/'.$id);
		}
		$this->form_view('Tambah Event', NULL);
	}

	public function ubah($id = 0)
	{
		$row = $this->event_model->find((int) $id) or show_404();
		$this->rules();
		if ($this->form_validation->run() && $this->relasi_ada())
		{
			$this->event_model->update($row->id_event, $this->data());
			$this->sukses('Event berhasil diperbarui.');
			redirect('event/detail/'.$row->id_event);
		}
		$this->form_view('Ubah Event', $row);
	}

	public function hapus($id = 0)
	{
		$this->wajib_post();
		$id = (int) $id;
		if ($this->event_model->jumlah_transaksi($id) > 0)
		{
			$this->gagal('Event tidak dapat dihapus karena sudah memiliki transaksi. Nonaktifkan saja bila tidak ingin menerima pemesanan.');
		}
		else
		{
			$this->event_model->delete($id);
			$this->sukses('Event berhasil dihapus.');
		}
		redirect('event');
	}

	private function form_view($title, $row)
	{
		$this->render('event/form', array(
			'title' => $title, 'icon' => 'bi-calendar-event', 'eyebrow' => 'Manajemen', 'subtitle' => 'Lengkapi informasi event, tanggal, dan lokasi.',
			'actions' => '<a class="btn btn-outline-secondary btn-sm" href="'.site_url('event').'"><i class="bi bi-arrow-left"></i> Kembali</a>',
			'row' => $row,
			'kategori' => $this->kategori_model->semua('nama_kategori'),
			'lokasi' => $this->lokasi_model->semua('nama_lokasi'),
		));
	}
}
