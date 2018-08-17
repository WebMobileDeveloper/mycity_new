<?php

class SearchLog extends CI_Model
{
	var $id ='';
	var $user_id='';
	var $vocation='';
	var $city='';
	var $created_at ='';
	  
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	} 
	public function add($data)
	{
		$this->db->insert("mc_business_search_log", $data);
		$studentid = $this->db->insert_id() ;
		return $studentid; 
	}
	
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('mc_business_search_log'); 
	}
	
	function getknow($member_id)
	{ 
		$this->db->select("*");
		$this->db->from("mc_business_search_log"); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
}


?>