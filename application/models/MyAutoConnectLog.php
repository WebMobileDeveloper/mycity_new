<?php

class MyAutoConnectLog  extends CI_Model
{
	var $id ='';
	var $sender ='';
	var $receipent =''; 
	var $cdate =''; 
	var $status =''; 


	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("mc_auto_connect_log", $data);
		$logid = $this->db->insert_id() ; 
		return $logid; 
	}
   
	function get_autoconnect_list($id)
	{ 
		$this->db->select("*");
		$this->db->from("mc_auto_connect_log"); 
		$this->db->where("receipent",  $id); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
}


?>