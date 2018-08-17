<?php

class MyClientTracking extends CI_Model
{
	var $id ='';
	var $client_id=''; 
	var $a_date=''; 
	var $description=''; 
	var $status='';  
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	} 
	  
	public function add($data)
	{
		$this->db->insert("mc_client_tracking", $data);
		$voicemailid = $this->db->insert_id() ;
		return $voicemailid; 
	}   
	public function update($data, $id)
	{
		$this->db->where('id', $id);  
		$this->db->update("mc_client_tracking", $data);
	} 
	public function remove($id)
    {
		$this->db->where('id', $id);
		$this->db->delete('mc_client_tracking'); 
	}
	
	function get_all_members($keyword, $status, $offset, $limit)
	{
		if($keyword !='')
			$where_name = " and username like  '$keyword%' ";
		else
			$where_name = " ";
		
		$members = $this->db->query("select id as a,  user_email as b,  user_pass as c, 
		username as d, user_phone as g,  image , 'None' sh 
		from mc_user where username <> '' and id<>'1' and  user_status='$status' $where_name order by username limit $offset, $limit ");
		
		$sql_query_count =  "select count(*) as reccnt from mc_user where username <> '' and id<>'1' and  user_status='$status' $where_name";
		$rst_count = $this->db->query($sql_query_count);
		$count = $rst_count->row()->reccnt;
		
		$i=0;
		foreach($members->result() as $row) 
		{
			$voicemailtrack  = $this->db->query("select a_date, description from mc_client_tracking where status='0' and client_id='". $row->a  ."' order by id desc");
			if($voicemailtrack->num_rows() > 0)
			{
				$row->sh  = $voicemailtrack->row()->description;
			}
			$i++;
		} 
		
		$result  = array('error' =>  '0' , 'errmsg' =>  'Matching members are found!', 
			'results' => $members, 'num_rows' => $count ); 
		return $result;
	}
	 
	
	function member_statusupdate($ids, $state )
	{
		$this->db->query("update mc_user set user_status = '$state' WHERE  id  in ( $ids  )");
		
	} 
	
	function get_voicemails( $data )
	{
		$offset  =  $data['offset'];
		$keyword =  $data['keyword'] ;   
		if($keyword !='')
			$where_name = " and username like  '$keyword%' ";
		else 
			$where_name = " ";
		$condition =  $data['condition'];
		
		if( !isset($data['limit']) || $data['limit'] == ''   )
		{
			$limit = 10;
		}
		else 
		{
			$limit=$data['limit'];
		}
		
		$members = $this->db->query("select id as a,  user_email as b,  user_pass as c,  username  ,  user_role as e, 
		user_pkg  as f,  user_phone as g,  image as h,   user_status as i,  group_status as j,
		'None' lastbroadcast, 'None' nextbroadcast, 'NA' da from mc_user where username <> '' and id <>'1' $where_name and  id $condition (select distinct client_id from  mc_client_tracking) order by username limit $offset , $limit ");
		 
		$sql_query_count =  "select count(*) as reccnt from mc_user where username <> '' and id<>'1' $where_name and id $condition (select distinct client_id from  mc_client_tracking)  ";
		$rst_count = $this->db->query($sql_query_count);
		$num_rows = $rst_count->row()->reccnt;
		 
		$i=0;
		foreach($members->result() as $row) 
		{
			//last broadcast
			$voicemailtrack  = $this->db->query("select a_date,status, description from mc_client_tracking where client_id='". $row->a  ."' order by id desc");
			if($voicemailtrack->num_rows() > 0)
			{
				foreach($voicemailtrack->result() as $item) 
				{
					if($item->status == 1)
					{
						$members->lastbroadcast = $item->a_date;
					}
					else  if($item->status == 0)
					{
						$members->nextbroadcast  = $item->a_date ;
						$members->da  = $item->description;
					}
				}  
			} 
			$i++;
		}  
		$jsonresult = array('error' =>  '0' , 'errmsg' =>  'Voicemail log fetched!', 
		'results' => $members, 'num_rows' => $num_rows ); 	 
		
		return $jsonresult ;
	}
	
	function get_voicemail_timeline($id)
	{ 
		$sql_query = "select a.id as id, b.id as mid, a.a_date as a, a.description as b,a.status as c, b.username as d   
		from mc_client_tracking as a inner join mc_user as b on a.client_id=b.id 
		where  client_id='$id' order by a_date " ; 
		$results = $this->db->query( $sql_query ); 
		$jsonresult = array('error' =>  '0' , 'errmsg' => 'Voicemails assigned are retrieved!',  
		'results' => $results );
		return $jsonresult; 
	}
}

?>