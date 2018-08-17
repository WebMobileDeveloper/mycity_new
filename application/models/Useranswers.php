<?php

class UserAnswers  extends CI_Model
{
	var $id ='';
	var $user_id =''; 
	var $question_id =''; 
	var $answer =''; 
	var $deleted ='';
	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("user_answers", $data);
		$answerid = $this->db->insert_id() ; 
		return $answerid; 
	} 
	
	public function get_answer($knowid)
	{
		$this->db->select("*");
		$this->db->where("user_id", $knowid );
		$this->db->from("user_answers");
		$result = $this->db->get();
		return $result;
	}
	
}

?>