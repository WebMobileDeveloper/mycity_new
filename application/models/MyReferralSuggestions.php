<?php

class MyReferralSuggestions extends CI_Model
{
	var $id ='';
	var $sender=''; 
	var $receipent=''; 
	var $subject = ''; 
	var $emailbody=''; 
	var $emailstatus='';
	var $senton='';
	var $email_type = ''; 
	var $associatedmember ='';
	var $replyto ='';
	 
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	} 
	public function add($data)
	{
		$this->db->insert("mc_mailbox", $data);
		$studentid = $this->db->insert_id() ;
		return $studentid; 
	} 
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('mc_newmailbox'); 
	}
	
	public function remove_batch($ids )
	{  
		$this->db->query(" delete from referralsuggestions where id in (" . $ids  . ")   ");
		 
	} 
	
}

?>