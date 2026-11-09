<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {

	/** Rekap per jenis tiket: kuota, terjual, sisa, pendapatan (lunas) */
	public function rekap($id_event = NULL)
	{
		$this->db->select("e.id_event, e.nama_event, e.tanggal_mulai, e.tanggal_selesai, jt.id_tiket, jt.nama_tiket, jt.harga, jt.kuota, jt.terjual,
				(jt.kuota - jt.terjual) AS sisa,
				COALESCE((SELECT SUM(t.total_harga) FROM transaksi t WHERE t.id_tiket = jt.id_tiket AND t.status = 'lunas'),0) AS pendapatan,
				COALESCE((SELECT SUM(t.jumlah) FROM transaksi t WHERE t.id_tiket = jt.id_tiket AND t.status = 'menunggu'),0) AS menunggu_bayar", FALSE)
			->from('jenis_tiket jt')
			->join('event e', 'e.id_event = jt.id_event');
		if ($id_event)
		{
			$this->db->where('e.id_event', (int) $id_event);
		}
		return $this->db->order_by('e.tanggal_mulai', 'DESC')->order_by('e.id_event')->order_by('jt.harga')->get()->result();
	}

	public function ringkasan()
	{
		$r = $this->db->query("SELECT
				(SELECT COUNT(*) FROM event WHERE status = 'aktif' AND tanggal_selesai >= ?) AS event_aktif,
				(SELECT COALESCE(SUM(terjual),0) FROM jenis_tiket) AS tiket_terjual,
				(SELECT COALESCE(SUM(kuota - terjual),0) FROM jenis_tiket jt JOIN event e ON e.id_event = jt.id_event
					WHERE e.status = 'aktif' AND e.tanggal_selesai >= ?) AS tiket_tersisa,
				(SELECT COALESCE(SUM(total_harga),0) FROM transaksi WHERE status = 'lunas') AS pendapatan,
				(SELECT COUNT(*) FROM transaksi WHERE status = 'menunggu') AS trx_menunggu,
				(SELECT COUNT(*) FROM transaksi) AS trx_total", array(date('Y-m-d'), date('Y-m-d')))->row();
		return $r;
	}

	public function event_mendatang($n = 5)
	{
		return $this->db->select("e.id_event, e.nama_event, e.tanggal_mulai, e.tanggal_selesai, l.nama_lokasi,
				COALESCE(SUM(jt.kuota),0) AS kuota, COALESCE(SUM(jt.terjual),0) AS terjual", FALSE)
			->from('event e')
			->join('lokasi l', 'l.id_lokasi = e.id_lokasi')
			->join('jenis_tiket jt', 'jt.id_event = e.id_event', 'left')
			->where('e.status', 'aktif')->where('e.tanggal_selesai >=', date('Y-m-d'))
			->group_by('e.id_event, e.nama_event, e.tanggal_mulai, e.tanggal_selesai, l.nama_lokasi')
			->order_by('e.tanggal_mulai')->limit($n)->get()->result();
	}
}
