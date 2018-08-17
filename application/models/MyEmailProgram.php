<?php

class MyEmailProgram extends CI_Model
{
	var $id ='';
	var $mail_heading=''; 
	var $email_body='';  
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	} 
	  
	public function add($data)
	{
		$this->db->insert("mc_email_program", $data);
		$emailprogramid = $this->db->insert_id() ;
		return $emailprogramid; 
	}   
	
	public function remove($id)
    {
		$this->db->where('id', $id);
		$this->db->delete('mc_email_program'); 
	} 
	 
}

?>