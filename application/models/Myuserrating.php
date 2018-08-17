<?php

class Myuserrating extends CI_Model
{
	var $id ='';
	var $grp_name='';
	var $islisted=''; 
	var $request_by='';   
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$grp_name = $data['grp_name'];
		$this->db->select("count(*) as cnt");
		$this->db->from("groups"); 
		$this->db->where("grp_name",  $grp_name  ); 
		$result  = $this->db->get();
		
		$row = $result->row();
		if($row->cnt == 0)
		{
			$this->db->insert("groups", $data);
			$grpid = $this->db->insert_id() ; 
			return $grpid;
		}
		else
		{
			return -1;
		}  
	} 
	function get_rating($mid)
	{
		$sql_query =  "select question_id, sum(ranking) as rank, count(rated_by) as rated_by from mc_user_rating 
		where user_id='$mid' group by question_id" ;
		$result = $this->db->query($sql_query ); 
		return  $result ; 
	} 
	
	
	function get_rating_details($mid)
	{
		$sql_query =  "select a.*, b.username 
		from mc_user_rating as a inner join mc_user as b 
		on a.rated_by=b.id 
		where a.user_id='$mid' order by username, question_id" ;
		$result = $this->db->query($sql_query ); 
		return  $result ; 
	} 
	
} 

?>