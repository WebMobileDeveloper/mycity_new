<?php

class Questions extends CI_Model
{
	var $id ='';
	var $question ='';
	var $question_type ='';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("questions", $data);
		$studentid = $this->db->insert_id() ; 
		return $studentid; 
	}
 

	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('questions'); 
	}
	
	function getquestions( )
	{ 
		$this->db->select("*");
		$this->db->from("questions"); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
	
	function get_questions_bytype($type )
	{ 
		$this->db->select("*");
		$this->db->from("questions"); 
		$this->db->where('question_type', $type ); 
		$result  = $this->db->get(); 
		return  $result ; 
		  
	}
}

?>