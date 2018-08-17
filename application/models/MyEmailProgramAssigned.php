<?php

class MyEmailProgramAssigned extends CI_Model
{
	var $id ='';
	var $mail_id=''; 
	var $client_id='';  
	var $assigned_date='';  
	var $mail_stage='';  
	var $status='';  
	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	} 
	  
	public function add($data)
	{
		$this->db->insert("mc_email_program_assigned", $data);
		$epassignid = $this->db->insert_id() ;
		return $epassignid; 
	}   
	
	public function remove($id)
    {
		$this->db->where('id', $id);
		$this->db->delete('mc_email_program_assigned'); 
	}  

	public function get_timeline($id)
	{
		$sql_query = "select a.id as seqid, a.assigned_date as d, a.mail_stage,a.status,  b.mail_heading, b.id as mailid 
		from mc_email_program_assigned as a inner join mc_email_program as b on a.mail_id=b.id 
		where  client_id='$id' order by mail_stage desc" ;   
		 
		$timeline = $this->db->query($sql_query);
		return $timeline; 
	}
	
}

?>