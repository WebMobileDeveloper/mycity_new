<?php

class McMailTemplates extends CI_Model
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
	
	
	public function get_templates( )
	{
		 
		$q = $this->db->query("select * from  mc_mail_templates  where status='0' order by templatename ");
		$mailtype ='';
		$data = array();
		foreach( $q->result() as $q_row)
		{
			 if($q_row->mailtype  == 0)
			{
				$mailtype='Trigger Mail';
			}
			else  if($q_row->mailtype == 1)
			{
				$mailtype='Introduction Mail';
			}
			else  if($q_row->mailtype == 2)
			{
				$mailtype='LinkedIn Invitation';
			}
			else  if($q_row->mailtype == 3)
			{
				$mailtype='Testimonial Videos';
			}
			
			$data[] = ["id" => $q_row->id , "template" => $q_row->templatename , 
			"subject" => $q_row->subject  , "mailbody" => $q_row->mailbody , "mailtype" => $mailtype ];
		}
		return $data;
	} 
	
}

?>