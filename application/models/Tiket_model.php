<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tiket_model extends MY_Model {
	protected $table = 'jenis_tiket';
	protected $pk = 'id_tiket';

	public function semua_lengkap($id_event = NULL)
	{
		$this->db->select('jt.*, (jt.kuota - jt.terjual) AS sisa, e.nama_event, e.tanggal_mulai, e.tanggal_selesai, e.status AS status_event')
			->from('jenis_tiket jt')
			->join('event e', 'e.id_event = jt.id_event');
		if ($id_event)
		{
			$this->db->where('jt.id_event', $id_event);
		}
		return $this->db->order_by('e.tanggal_mulai', 'DESC')->order_by('jt.harga')->get()->result();
	}

	/** Tiket yang bisa dipesan (event aktif & belum selesai) */
	public function tersedia_dipesan()
	{
		return $this->db->select('jt.*, (jt.kuota - jt.terjual) AS sisa, e.nama_event, e.tanggal_mulai, e.tanggal_selesai')
			->from('jenis_tiket jt')
			->join('event e', 'e.id_event = jt.id_event')
			->where('e.status', 'aktif')
			->where('e.tanggal_selesai >=', date('Y-m-d'))
			->order_by('e.tanggal_mulai')->order_by('jt.harga')
			->get()->result();
	}

	public function dipakai($id)
	{
		return $this->db->where('id_tiket', $id)->count_all_results('transaksi') > 0;
	}
}
