<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {

	public function __construct($rules = array())
	{
		parent::__construct($rules);

		// Pesan kesalahan berbahasa Indonesia
		$this->set_message('required', '{field} wajib diisi.');
		$this->set_message('isset', '{field} wajib diisi.');
		$this->set_message('valid_email', '{field} harus berupa alamat email yang valid.');
		$this->set_message('numeric', '{field} harus berupa angka.');
		$this->set_message('integer', '{field} harus berupa bilangan bulat.');
		$this->set_message('is_natural', '{field} harus berupa bilangan bulat tidak negatif.');
		$this->set_message('is_natural_no_zero', '{field} harus berupa bilangan bulat lebih dari 0.');
		$this->set_message('greater_than', '{field} harus lebih dari {param}.');
		$this->set_message('greater_than_equal_to', '{field} tidak boleh kurang dari {param} (tidak boleh negatif).');
		$this->set_message('less_than_equal_to', '{field} tidak boleh lebih dari {param}.');
		$this->set_message('max_length', '{field} maksimal {param} karakter.');
		$this->set_message('min_length', '{field} minimal {param} karakter.');
		$this->set_message('in_list', '{field} tidak valid.');
		$this->set_message('regex_match', 'Format {field} tidak valid.');
		$this->set_message('valid_date', '{field} bukan tanggal yang valid (format YYYY-MM-DD).');
		$this->set_message('tgl_tidak_sebelum', '{field} tidak boleh lebih awal dari tanggal mulai.');
	}

	/** Tanggal valid format YYYY-MM-DD (menolak mis. 2026-02-31) */
	public function valid_date($str)
	{
		$d = DateTime::createFromFormat('Y-m-d', (string) $str);
		return ($d && $d->format('Y-m-d') === $str);
	}

	/** Tanggal selesai tidak boleh sebelum tanggal mulai. Pemakaian: tgl_tidak_sebelum[tanggal_mulai] */
	public function tgl_tidak_sebelum($str, $field)
	{
		$mulai = isset($_POST[$field]) ? $_POST[$field] : '';
		if ( ! $this->valid_date($str) OR ! $this->valid_date($mulai))
		{
			return TRUE; // dicek oleh aturan valid_date
		}
		return $str >= $mulai;
	}
}
