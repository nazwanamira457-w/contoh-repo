<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('rupiah'))
{
	function rupiah($angka)
	{
		return 'Rp '.number_format((float) $angka, 0, ',', '.');
	}
}

if ( ! function_exists('tgl_indo'))
{
	function tgl_indo($tanggal, $dengan_jam = FALSE)
	{
		if (empty($tanggal) OR strpos($tanggal, '0000-00-00') === 0)
		{
			return '-';
		}
		$bulan = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des');
		$ts = strtotime($tanggal);
		$out = date('d', $ts).' '.$bulan[(int) date('n', $ts)].' '.date('Y', $ts);
		return $dengan_jam ? $out.', '.date('H:i', $ts) : $out;
	}
}

/* Pecah tanggal: array(hari, bulan singkat, tahun) */
if ( ! function_exists('tgl_parts'))
{
	function tgl_parts($tanggal)
	{
		$bulan = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des');
		$ts = strtotime($tanggal);
		return array(date('j', $ts), $bulan[(int) date('n', $ts)], date('Y', $ts));
	}
}

if ( ! function_exists('rentang_tgl'))
{
	function rentang_tgl($mulai, $selesai)
	{
		return ($mulai === $selesai) ? tgl_indo($mulai) : tgl_indo($mulai).' - '.tgl_indo($selesai);
	}
}

/**
 * Status waktu event: Akan Datang / Berlangsung / Selesai
 */
if ( ! function_exists('status_waktu_event'))
{
	function status_waktu_event($mulai, $selesai)
	{
		$hari_ini = date('Y-m-d');
		if ($selesai < $hari_ini)
		{
			return array('Selesai', 'text-bg-secondary');
		}
		if ($mulai > $hari_ini)
		{
			return array('Akan Datang', 'text-bg-primary');
		}
		return array('Berlangsung', 'text-bg-success');
	}
}

if ( ! function_exists('event_selesai'))
{
	function event_selesai($tanggal_selesai)
	{
		return $tanggal_selesai < date('Y-m-d');
	}
}

if ( ! function_exists('badge_status'))
{
	function badge_status($status)
	{
		$map = array(
			'menunggu' => array('Menunggu', 'text-bg-warning'),
			'lunas'    => array('Lunas', 'text-bg-success'),
			'batal'    => array('Batal', 'text-bg-secondary'),
			'aktif'    => array('Aktif', 'text-bg-success'),
			'nonaktif' => array('Nonaktif', 'text-bg-secondary'),
		);
		$m = isset($map[$status]) ? $map[$status] : array($status, 'text-bg-light');
		return '<span class="badge '.$m[1].'">'.html_escape($m[0]).'</span>';
	}
}

/* Atribut selected bila nilai sama */
if ( ! function_exists('sel'))
{
	function sel($saat_ini, $nilai)
	{
		return ((string) $saat_ini === (string) $nilai) ? ' selected' : '';
	}
}

/* Tambahkan class is-invalid bila field punya error validasi */
if ( ! function_exists('inv'))
{
	function inv($field)
	{
		return form_error($field) !== '' ? ' is-invalid' : '';
	}
}

if ( ! function_exists('err'))
{
	function err($field)
	{
		return form_error($field, '<div class="invalid-feedback d-block">', '</div>');
	}
}
