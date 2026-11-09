<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_model extends MY_Model {
	protected $table = 'event';
	protected $pk = 'id_event';

	public function semua_lengkap()
	{
		return $this->db->select('e.*, k.nama_kategori, l.nama_lokasi, l.kota,
				COALESCE((SELECT SUM(jt.kuota) FROM jenis_tiket jt WHERE jt.id_event = e.id_event),0) AS total_kuota,
				COALESCE((SELECT SUM(jt.terjual) FROM jenis_tiket jt WHERE jt.id_event = e.id_event),0) AS total_terjual', FALSE)
			->from('event e')
			->join('kategori k', 'k.id_kategori = e.id_kategori')
			->join('lokasi l', 'l.id_lokasi = e.id_lokasi')
			->order_by('e.tanggal_mulai', 'DESC')
			->get()->result();
	}

	public function detail($id)
	{
		return $this->db->select('e.*, k.nama_kategori, l.nama_lokasi, l.alamat, l.kota')
			->from('event e')
			->join('kategori k', 'k.id_kategori = e.id_kategori')
			->join('lokasi l', 'l.id_lokasi = e.id_lokasi')
			->where('e.id_event', $id)->get()->row();
	}

	/** Event yang masih menerima pemesanan: aktif dan belum selesai */
	public function terbuka()
	{
		return $this->db->where('status', 'aktif')
			->where('tanggal_selesai >=', date('Y-m-d'))
			->order_by('tanggal_mulai')->get('event')->result();
	}

	/** Event terbuka beserta nama lokasi/kategori (untuk Loket) */
	public function semua_lengkap_terbuka($events)
	{
		$ids = array();
		foreach ($events as $e) { $ids[] = (int) $e->id_event; }
		if ( ! $ids) { return array(); }
		return $this->db->select('e.*, k.nama_kategori, l.nama_lokasi, l.alamat, l.kota')
			->from('event e')
			->join('kategori k', 'k.id_kategori = e.id_kategori')
			->join('lokasi l', 'l.id_lokasi = e.id_lokasi')
			->where_in('e.id_event', $ids)
			->order_by('e.tanggal_mulai')->get()->result();
	}

	public function jumlah_transaksi($id)
	{
		return $this->db->from('transaksi t')
			->join('jenis_tiket jt', 'jt.id_tiket = t.id_tiket')
			->where('jt.id_event', $id)->count_all_results();
	}
}
