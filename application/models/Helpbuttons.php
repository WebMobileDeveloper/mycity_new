<?php

class Helpbuttons extends CI_Model
{
	var $id =''; 
	var $helptitle=''; 
	var $helpvideo=''; 
	var $publish=''; 
	var $position='';  
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("helpsbuttons", $data);
		$studentid = $this->db->insert_id() ; 
		return $studentid; 
	}
  
public function update($data, $id)
	{
		$this->db->where("id", $id);
		$this->db->update("helpsbuttons", $data);  
	} 

	
	
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('helpsbuttons'); 
	}
	
	function getbuttons( )
	{ 
		$this->db->select("*");
		$this->db->from("helpsbuttons"); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
	function get_button_by_id($id )
	{ 
		$this->db->select("*");
		$this->db->from("helpsbuttons"); 
		$this->db->where("id", $id);
		$result  = $this->db->get(); 
		return  $result ; 
	}
}

?>