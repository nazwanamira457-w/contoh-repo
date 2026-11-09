<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kategori_model extends MY_Model {
	protected $table = 'kategori';
	protected $pk = 'id_kategori';

	public function semua_dengan_jumlah()
	{
		return $this->db->select('k.*, (SELECT COUNT(*) FROM event e WHERE e.id_kategori = k.id_kategori) AS jml_event', FALSE)
			->from('kategori k')->order_by('k.nama_kategori')->get()->result();
	}

	public function dipakai($id)
	{
		return $this->db->where('id_kategori', $id)->count_all_results('event') > 0;
	}
}
