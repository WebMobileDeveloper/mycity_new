<?php

class MyLoginLog extends CI_Model
{
	var $id =''; 
	var $userid=''; 
	var $logintime=''; 
	var $logouttime=''; 
	var $token='';  
	var $remembertoken='';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	} 
	  
	
	function get_todays_log()
	{
		$today = date('Y-m-d'); 
		$this->db->distinct();
		$this->db->select("userid"); 
		$this->db->from("mc_login_log"); 
		$this->db->where("date(logintime) = ",  $today ); 
		$this->db->where("userid <> ",  1 ); 
		$result = $this->db->get(); 
		return  $result ;  
	}
	
	
}

?>