<?php

class model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	public function insert($table,$data)
	{
		$this->db->insert($table,$data);
		return true;
	}
	public function insert_batch($table,$data)
	{
		$this->db->insert_batch($table,$data);
		return true;
	}
	public function update($table,$where,$data)
	{
		$this->db->where($where);
		$this->db->update($table,$data);
		return true;
	}
	public function delete($table,$where)
	{
		$this->db->where($where);
		$this->db->delete($table);
		return true;
	}
	public function query_exec($query,$status)
	{
		$query = $this->db->query($query);
		if($status == 'result'){
			return $query->result();
		}else{
			return $query->row();
		}
	}
	public function gd($table,$select,$where,$status)
	{
		$this->db->where($where);
		$this->db->select($select);
		$this->db->from($table);
		if($status == 'result'){
			return $this->db->get()->result();
		}else{
			return $this->db->get()->row();
		}
	}
}