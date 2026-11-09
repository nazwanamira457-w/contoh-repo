<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller {

	protected $public = TRUE;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}

	/** Halaman awal setelah login: pembeli kembali ke landing page, admin ke dashboard */
	private function beranda($role)
	{
		return $role === 'pembeli' ? '' : 'dashboard';
	}

	private $pola_next = '#^(loket|loket/pesan/[0-9]+|kasir)$#';

	/** Simpan tujuan setelah login (mis. dari tombol Pesan di landing page) */
	private function simpan_next()
	{
		$next = (string) $this->input->get('next');
		if ($next !== '' && preg_match($this->pola_next, $next))
		{
			$this->session->set_userdata('next', $next);
		}
	}

	private function tujuan($role)
	{
		$next = (string) $this->session->userdata('next');
		$this->session->unset_userdata('next');
		if ($role === 'pembeli' && $next !== '' && preg_match($this->pola_next, $next))
		{
			return $next;
		}
		return $this->beranda($role);
	}

	private function mulai_sesi($user)
	{
		$this->session->sess_regenerate(TRUE);
		$this->session->set_userdata(array(
			'logged_in'  => TRUE,
			'id_user'    => $user->id_user,
			'nama'       => $user->nama,
			'role'       => $user->role,
			'id_pemesan' => $user->id_pemesan,
		));
	}

	public function index()
	{
		$this->simpan_next();
		if ($this->session->userdata('logged_in'))
		{
			redirect($this->beranda($this->session->userdata('role')));
		}

		if ($this->input->method() === 'post')
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required');
			$this->form_validation->set_rules('password', 'Password', 'required');

			if ($this->form_validation->run())
			{
				$user = $this->user_model->by_username($this->input->post('username', TRUE));
				if ($user && password_verify($this->input->post('password'), $user->password))
				{
					$this->mulai_sesi($user);
					redirect($this->tujuan($user->role));
				}
				$this->gagal('Username atau password salah.');
				redirect('auth');
			}
		}

		$this->load->view('auth/login');
	}

	/** Pendaftaran akun pembeli */
	public function daftar()
	{
		$this->simpan_next();
		if ($this->session->userdata('logged_in'))
		{
			redirect($this->beranda($this->session->userdata('role')));
		}

		if ($this->input->method() === 'post')
		{
			$this->form_validation->set_rules('nama', 'Nama lengkap', 'trim|required|max_length[100]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[100]');
			$this->form_validation->set_rules('no_hp', 'No. HP', 'trim|required|regex_match[/^\+?[0-9]{8,15}$/]');
			$this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_dash|min_length[4]|max_length[50]|is_unique[users.username]');
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[72]');
			$this->form_validation->set_rules('password2', 'Konfirmasi password', 'required|matches[password]');
			$this->form_validation->set_message('is_unique', '{field} sudah dipakai, pilih yang lain.');
			$this->form_validation->set_message('matches', '{field} tidak sama dengan password.');
			$this->form_validation->set_message('alpha_dash', '{field} hanya boleh huruf, angka, _ dan -.');
			$this->form_validation->set_message('regex_match', '{field} harus 8-15 digit angka (boleh diawali +).');

			if ($this->form_validation->run())
			{
				$user = $this->user_model->daftar_pembeli(array(
					'nama'  => $this->input->post('nama', TRUE),
					'email' => $this->input->post('email', TRUE),
					'no_hp' => $this->input->post('no_hp', TRUE),
				), $this->input->post('username', TRUE), password_hash($this->input->post('password'), PASSWORD_DEFAULT));

				if ($user)
				{
					$this->mulai_sesi($user);
					$this->sukses('Akun berhasil dibuat. Selamat datang!');
					redirect($this->tujuan('pembeli'));
				}
				$this->gagal_now('Pendaftaran gagal, coba lagi.');
			}
		}

		$this->load->view('auth/daftar', array('pesan' => $this->pesan_langsung));
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('auth');
	}
}
