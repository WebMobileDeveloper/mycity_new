<?php

class Settings extends CI_Model
{
	var $id ='';
	var $skey =''; 
	var $svalue =''; 
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("mc_settings", $data);
		$studentid = $this->db->insert_id() ; 
		return $studentid; 
	}
	
	
	function get_config($key)
	{ 
		$this->db->select("*");
		$this->db->from("mc_settings");
		$this->db->where("skey", $key);
		$result  = $this->db->get(); 
		return  $result ; 
	}
}

?>