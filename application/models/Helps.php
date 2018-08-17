<?php

class Helps extends CI_Model
{
	var $id ='';
	var $id ='';
	var $helptitle =''; 
	var $helptext =''; 
	var $helpvideo =''; 
	var $publish =''; 
	var $positionvar='';
	
	 
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("helps", $data);
		$studentid = $this->db->insert_id() ; 
		return $studentid; 
	}
  
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('helps'); 
	}
	
	function getknow($member_id)
	{ 
		$this->db->select("*");
		$this->db->from("helps"); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
}


?>