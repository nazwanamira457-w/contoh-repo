<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model {
	protected $table = 'users';
	protected $pk = 'id_user';

	/** Daftar akun pembeli baru + data pemesan terkait, atomik */
	public function daftar_pembeli($pemesan, $username, $password_hash)
	{
		$this->db->trans_begin();
		$this->db->insert('pemesan', $pemesan);
		$id_pemesan = $this->db->insert_id();
		$this->db->insert('users', array(
			'nama'       => $pemesan['nama'],
			'username'   => $username,
			'password'   => $password_hash,
			'role'       => 'pembeli',
			'id_pemesan' => $id_pemesan,
		));
		$id_user = $this->db->insert_id();
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return FALSE;
		}
		$this->db->trans_commit();
		return $this->find($id_user);
	}

	public function by_username($username)
	{
		return $this->db->get_where('users', array('username' => $username))->row();
	}
}
