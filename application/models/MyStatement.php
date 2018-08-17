<?php

class MyStatement extends CI_Model
{
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
		$this->db->insert("mc_statements", $data);
		$noteid = $this->db->insert_id() ; 
		return $noteid; 
	}
  
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('mc_statements'); 
	}
	
	function get_statements()
	{ 
		$this->db->select("*");
		$this->db->from("mc_statements"); 
		$this->db->order_by("id", 'desc');
		$result  = $this->db->get(); 
		return  $result ; 
	}
	  
}


?>