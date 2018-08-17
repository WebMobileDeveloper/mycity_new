<?php

class MyGlobalSearchLog  extends CI_Model
{
	var $id ='';
	var $user_id ='';
	var $keyword =''; 
	var $city_zip =''; 
	  
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("mc_global_search_log", $data);
		$logid = $this->db->insert_id() ; 
		return $logid; 
	}
   
	function get_logs($id)
	{ 
		$this->db->select("*");
		$this->db->from("mc_global_search_log");  
		$result  = $this->db->get(); 
		return  $result ; 
	}
}


?>