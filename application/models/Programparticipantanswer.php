<?php

class Programparticipantanswer extends CI_Model
{
	var $id ='';
	var $client_id=''; 
	var $program_id=''; 
	var $relation_id='';  
	var $question_no='';  
	var $answer='';  
	var $add_answer='';  
	var $qdate='';  
	var $adate='';  
	var $reminder_sent='';
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("mc_program_client_answer", $data);
		$studentid = $this->db->insert_id() ;
		return $studentid; 
	} 
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('mc_program_client_answer'); 
	}
	
	function getall()
	{
		$this->db->select("id as a,  program_id as b,  question as c ,  answer_form as d");
		$this->db->from("mc_program_client_answer");
		$result  = $this->db->get();
		return  $result ; 
	}
	
	
 function activity_log($data)
 { 
	$pid =   $data['pid'];   
	$mid =   $data['mid']; 
	$sql_query = "select t1.answer as a, t1.add_answer as b, t1.adate as c, t1.relation_id as d, t2.client_name as e 
	from mc_program_client_answer as t1 
	inner join user_people as t2 on t1.relation_id=t2.id 
	where t1.client_id='$mid' and t1.program_id='$pid' and t1.adate <> '' order by t1.adate desc" ; 
	$rst = $this->db->query($sql_query);    
	return $rst;
 } 


}
?>