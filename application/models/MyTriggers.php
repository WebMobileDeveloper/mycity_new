<?php

class MyTriggers extends CI_Model
{
	var $id =''; 
	var $trigger_question=''; 
	var $entry_date=''; 
	var $user_id=''; 
	var $status='';
	  
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	 
	public function add($data, $id)
	{
		if($id  > 0)
		{
			$this->db->where("id",  $id);
			$this->db->update("my_triggers", $data);  
			$affectedrow = $this->db->affected_rows();
			return $affectedrow;  
		}
		else 
		{
			$this->db->insert("my_triggers", $data);
			$reminder_id = $this->db->insert_id() ; 
			return $reminder_id; 
		} 
		
	}
	public function get_triggers($user_id)
	{
		$this->db->select("*");
		$this->db->where("user_id", $user_id);
		$this->db->from("my_triggers"); 
		$result  = $this->db->get(); 
		return $result; 
	}
	 
}


?>