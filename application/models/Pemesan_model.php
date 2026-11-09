<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pemesan_model extends MY_Model {
	protected $table = 'pemesan';
	protected $pk = 'id_pemesan';

	public function semua_dengan_jumlah()
	{
		return $this->db->select("p.*, (SELECT COUNT(*) FROM transaksi t WHERE t.id_pemesan = p.id_pemesan) AS jml_transaksi", FALSE)
			->from('pemesan p')->order_by('p.nama')->get()->result();
	}

	public function dipakai($id)
	{
		return $this->db->where('id_pemesan', $id)->count_all_results('transaksi') > 0
			OR $this->db->where('id_pemesan', $id)->count_all_results('users') > 0;
	}
}
