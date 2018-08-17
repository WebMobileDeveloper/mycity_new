<?php

class Programquestion extends CI_Model
{
	 var $id ='';
	 var $program_id=''; 
	 var $question=''; 
	 var $answer_form='';  
	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("mc_program_questions", $data);
		$studentid = $this->db->insert_id() ;
		return $studentid; 
	} 
	
	public function update($data, $id)
	{
		$this->db->where("id", $id);
		$this->db->update("mc_program_questions", $data);  
	} 
	
	
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('mc_program_questions'); 
	}
	
	function getquestion($qid)
	{
		$this->db->select("*");
		$this->db->from("mc_program_questions"); 
		$this->db->where("id", $qid);
		$result  = $this->db->get(); 
		return  $result ; 
	}  
	
	function getall()
	{
		$this->db->select("id as a,  program_id as b,  question as c ,  answer_form as d");
		$this->db->from("mc_program_questions");  
		$result  = $this->db->get(); 
		return  $result ; 
	} 
	
	
}

?>