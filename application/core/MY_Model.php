<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model dasar CRUD sederhana.
 * Turunan cukup mengisi $table dan $pk.
 */
class MY_Model extends CI_Model {

	protected $table = '';
	protected $pk = 'id';

	public function semua($order = NULL)
	{
		if ($order) { $this->db->order_by($order); }
		return $this->db->get($this->table)->result();
	}

	public function find($id)
	{
		return $this->db->get_where($this->table, array($this->pk => $id))->row();
	}

	public function insert($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($id, $data)
	{
		return $this->db->where($this->pk, $id)->update($this->table, $data);
	}

	public function delete($id)
	{
		return $this->db->where($this->pk, $id)->delete($this->table);
	}
}
