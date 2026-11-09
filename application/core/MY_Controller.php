<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	/** Controller publik (mis. Auth) tidak memerlukan login */
	protected $public = FALSE;

	/** Role yang boleh membuka controller ini (admin = pengelola, pembeli = Loket & Kasir) */
	protected $roles = array('admin');

	public function __construct()
	{
		parent::__construct();

		if ( ! $this->public && ! $this->session->userdata('logged_in'))
		{
			redirect('auth');
		}

		if ( ! $this->public && ! in_array($this->session->userdata('role'), $this->roles, TRUE))
		{
			// Role tidak berhak: arahkan ke halaman utama role-nya sendiri
			$role = $this->session->userdata('role');
			if ($role === 'pembeli') { redirect('loket'); }
			if ($role === 'admin')   { redirect('dashboard'); }
			redirect('auth/logout');
		}
	}

	protected function render($view, $data = array())
	{
		$data['menu_aktif'] = $this->uri->segment(1) ?: 'dashboard';
		$data['pesan_langsung'] = $this->pesan_langsung;
		$data['user_role'] = $this->session->userdata('role');
		$data['user_nama'] = $this->session->userdata('nama') ?: 'Admin';

		$this->load->view('layout/header', $data);
		$this->load->view($view, $data);
		$this->load->view('layout/footer', $data);
	}

	/** Pesan galat yang ditampilkan pada halaman yang sama (tanpa redirect) */
	protected $pesan_langsung = NULL;
	protected function gagal_now($pesan) { $this->pesan_langsung = $pesan; }

	protected function sukses($pesan) { $this->session->set_flashdata('sukses', $pesan); }
	protected function gagal($pesan)  { $this->session->set_flashdata('gagal', $pesan); }

	/** Hanya terima POST (untuk aksi hapus / ubah status) */
	protected function wajib_post()
	{
		if ($this->input->method() !== 'post')
		{
			show_error('Metode tidak diizinkan.', 405);
		}
	}

	protected function paginasi($base_url, $total, $per_page)
	{
		$this->load->library('pagination');
		$this->pagination->initialize(array(
			'base_url'             => $base_url,
			'total_rows'           => $total,
			'per_page'             => $per_page,
			'page_query_string'    => TRUE,
			'query_string_segment' => 'hal',
			'use_page_numbers'     => TRUE,
			'reuse_query_string'   => TRUE,
			'num_links'            => 2,
			'full_tag_open'        => '<ul class="pagination pagination-sm mb-0">',
			'full_tag_close'       => '</ul>',
			'first_link'           => '&laquo;',
			'last_link'            => '&raquo;',
			'next_link'            => '&rsaquo;',
			'prev_link'            => '&lsaquo;',
			'first_tag_open'       => '<li class="page-item">',
			'first_tag_close'      => '</li>',
			'last_tag_open'        => '<li class="page-item">',
			'last_tag_close'       => '</li>',
			'next_tag_open'        => '<li class="page-item">',
			'next_tag_close'       => '</li>',
			'prev_tag_open'        => '<li class="page-item">',
			'prev_tag_close'       => '</li>',
			'cur_tag_open'         => '<li class="page-item active"><span class="page-link">',
			'cur_tag_close'        => '</span></li>',
			'num_tag_open'         => '<li class="page-item">',
			'num_tag_close'        => '</li>',
			'attributes'           => array('class' => 'page-link'),
		));
		return $this->pagination->create_links();
	}

	/** Kirim file bukti pembayaran (disimpan di luar folder publik) ke browser */
	protected function kirim_bukti($nama)
	{
		$nama = basename((string) $nama);
		$path = APPPATH.'uploads_bukti/'.$nama;
		if ($nama === '' OR ! is_file($path))
		{
			show_404();
		}
		$ext  = strtolower(pathinfo($nama, PATHINFO_EXTENSION));
		$tipe = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'pdf' => 'application/pdf');
		if ( ! isset($tipe[$ext])) { show_404(); }
		header('Content-Type: '.$tipe[$ext]);
		header('Content-Length: '.filesize($path));
		header('X-Content-Type-Options: nosniff');
		header('Content-Disposition: inline; filename="bukti-'.$nama.'"');
		readfile($path);
		exit;
	}
}
