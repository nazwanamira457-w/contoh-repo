<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lokasi_model extends MY_Model {
	protected $table = 'lokasi';
	protected $pk = 'id_lokasi';

	public function semua_dengan_jumlah()
	{
		return $this->db->select('l.*, (SELECT COUNT(*) FROM event e WHERE e.id_lokasi = l.id_lokasi) AS jml_event', FALSE)
			->from('lokasi l')->order_by('l.nama_lokasi')->get()->result();
	}

	public function dipakai($id)
	{
		return $this->db->where('id_lokasi', $id)->count_all_results('event') > 0;
	}
}
