<?php

class Programparticipant extends CI_Model
{
	var $id ='';
	var $client_id=''; 
	var $program_id=''; 
	var $join_date=''; 
	var $status=''; 
	var $clients_selected='';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("mc_program_client", $data);
		$studentid = $this->db->insert_id() ;
		return $studentid; 
	} 
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('mc_program_client'); 
	} 
	
	function getall()
	{
		$this->db->select("id as a,  program_id as b,  question as c ,  answer_form as d");
		$this->db->from("mc_program_client");  
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
	function get_participants( $data )
	{
		$programid =  $data['program']; 
		$mid =   $data['mid']; 
		 
		if($mid ==0 )
		{
			$sql_query = "select a.id as a,  a.client_id as b, a.program_id as c, a.join_date as d , a.status as e , 
			a.clients_selected as f, b.username as un, b.image as h, 'na' relations from mc_program_client as a inner join mc_user as b 
			on a.client_id=b.id where a.clients_selected <> '' and a.program_id='$programid' order by username " ; 
			$results = $this->db->query($sql_query);
			 
			$i=0;
			foreach($results->result() as $row) 
			{
				$relationships = $row->f;
				$ids = json_decode($relationships, TRUE );
				$ids =  implode(",", array_values($ids) )  ;
				 
				$rsrelations  = $this->db->query("select id as a, client_name as b from user_people where id in (". $ids .") order by client_name");
				if($rsrelations->num_rows() > 0)
				{ 
					$results->relations = $rsrelations;
				} 
				$i++;
			}
		}
		else 
		{
			$sql_query = "select a.id as a,  a.client_id as b, a.program_id as c, a.join_date as d , a.status as e , 
			a.clients_selected as f, b.username as un, b.image as h, 'na' relations from mc_program_client as a inner join mc_user as b 
			on a.client_id=b.id where b.id='$mid'  and a.program_id='$programid' order by username " ; 
			$results = $this->db->query($sql_query);
			 
			$i=0;
			foreach($results->result() as $row) 
			{
				$relationships = $row->f;
				if(  $relationships  != null )
				{
					$ids = json_decode($relationships, TRUE );
					$ids =  implode(",", array_values($ids) )  ;
					 
					$rsrelations  = $this->db->query("select id as a, client_name as b from user_people where id in (". $ids .") order by client_name");
					if($rsrelations->num_rows() > 0)
					{
						$results->relations  = $rsrelations;
					} 
				}
				$i++;
			} 
		} 
		return $results;
	} 
	
	function relationtimeline($data)
	{
		$relid =   $data['relid']; 
		$mid =   $data['mid']; 
		$pid =   $data['pid'];
		$sql_query = "select a.id as i, b.id as qno, question as q, answer as a, answer_form as af, a.add_answer as ads  from mc_program_client_answer as a inner join mc_program_questions as b on a.question_no=b.id where client_id='$mid' and a.program_id='$pid' and a.relation_id='$relid' ";
		$result = $this->db->query( $sql_query  ); 
		return  $result ; 
	}
	
	function join_program($data)
	{ 
		$eid = $data['id']; 
		$state = $data['s']; 
		$ppid = $data['ppid'];  
		if($ppid !='' && $ppid > 0)
		{
			//update
			$sql_query = "update mc_program_client set status='$state' where id='$ppid'" ;
			$stmt = $this->db->prepare($sql_query);
			$jsonresult = array('error' =>  '0' ,  'errmsg'  =>  'Program joined successfully!'  );
		}
		else
		{
			//insert after checking
			$sql_query = "select count(*) as reccnt from mc_program_client where client_id='$eid'" ;
			$rst = $this->db->query($sql_query);
			if($rst->num_rows() > 0)
			{
				$count = $rst->row()->reccnt ;
				if($count ==  0)
				{
					if(  $state != '' && $state  != 0)
					{
						$sql_query = "insert into mc_program_client (client_id, program_id, join_date, status ) values ( ?, ?, NOW(), '1' )" ;
					}
					else
					{
						$sql_query = "insert into mc_program_client (client_id, program_id, join_date ) values ( ?, ?, NOW() )" ;
					}
					$stmt = $this->db->query($sql_query, array($eid ,  '1'  ) );
					$jsonresult = array('error' =>  '0'  ,    'errmsg'  =>   'Program invite sent successfully!'  );  
				} 
				else 
				{
					$jsonresult = array('error' =>  '10' ,  'errmsg'  =>   'Program invite already sent!'  ); 
				}
			}
		}   
		return json_encode($jsonresult); 
	}  
}

?>