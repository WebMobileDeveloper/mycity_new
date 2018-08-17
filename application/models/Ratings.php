<?php

class Ratings extends CI_Model
{
	var $id ='';
	var $user_id=''; 
	var $question_id=''; 
	var $ranking='';
	  
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("user_rating", $data);
		$studentid = $this->db->insert_id() ; 
		return $studentid; 
	}
	
	public function add_batch($data)
	{
		$this->db->insert_batch("user_rating", $data); 
		 
	}
  
  
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('user_rating'); 
	}
	
	function getknow($member_id)
	{ 
		$this->db->select("*");
		$this->db->from("user_rating"); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
}


?>