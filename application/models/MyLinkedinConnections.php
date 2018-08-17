<?php

class MyLinkedinConnections  extends CI_Model
{
	var $id ='';
	var $fullname=''; 
	var $email=''; 
	var $company=''; 
	var $profession=''; 
	var $tag='';
	var $entrydate=''; 
	var $userid=''; 
	var $subject=''; 
	var $mailbody=''; 
	var $senddate=''; 
	var $mailsent='';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	} 
	
	public function add($data)
	{
		$this->db->insert("mc_linkedin_import", $data);
		$studentid = $this->db->insert_id() ; 
		return $studentid; 
	}  
}

?>