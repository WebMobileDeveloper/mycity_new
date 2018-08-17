<?php

class Mc_Business  extends CI_Model
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
	 
	function search_nearby($member_id)
	{
		$this->db->select("*");
		$this->db->from("mc_user"); 	
		$result  = $this->db->get(); 
		return  $result ; 
	}
}


?>