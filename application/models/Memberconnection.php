<?php

class Memberconnection  extends CI_Model
{
	
	  var $id=''; 
	  var $firstpartner=''; 
	  var $request_type=''; 
	  var $secondpartner=''; 
	  var $requestdate =''; 
	  var $approvedon ='';  
	  var $status =''; 
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	 
	public function add($data)
	{
		$reccnt = $this->db->query("select count(*) as tcnt from mc_member_connections 
		where firstpartner='" . $data['firstpartner'] . "' and 
		secondpartner ='" . $data['secondpartner'] . "'");
		
		$conid = -1;
		if($reccnt->num_rows() > 0)
		{
			
			if($reccnt->row()->tcnt == 0)
			{
				$this->db->insert("mc_member_connections", $data);
				$conid = $this->db->insert_id() ; 
			}
			else 
			{
				$conid = -1;
			} 
		}  
		return $conid; 
	} 
	 
	public function get_all_connections($data)
	{
		$userid = $data['userid'];   
		$offset = $data['offset'];    
		$rstatus = 0; //$data['rstatus']; 
		$pagesize =  $data['pagesize'];
		$status_where =''; 
		$status_where = "" ;//  " and status ='"  . $rstatus . "'"; 
		$ids = array(); 
		$sendlist = $this->db->query("(select distinct firstpartner as pid  from mc_member_connections where secondpartner='$userid'   ) 
		 union 
		( select distinct secondpartner as pid from mc_member_connections where firstpartner='$userid'   )  ");
		if($sendlist->num_rows() > 0)
		{
			foreach($sendlist->result()  as $item)
			{
				$ids[] = $item->pid;
			}
		}
		 
		
		if(sizeof($ids) > 0)
		{ 
			$con_ids = implode(',', array_unique($ids) );
			$sql_query = "select b.*, c.vocations, c.current_company,  c.city, c.zip, c.country  
			from mc_user as b inner join user_details as c 
			on c.user_id=b.id 
			where b.id in ($con_ids) order by b.username  limit $offset, $pagesize";  
			$allmembers = $this->db->query($sql_query);  
			
			$sql_query_count = "select count(*) as cnt 
			from mc_user as b inner join user_details as c 
			on c.user_id=b.id 
			where b.id in ($con_ids) order by b.username  ";  
			$allmembers_count = $this->db->query($sql_query_count);   
			$num_rows =  $allmembers_count->row()->cnt ;
			$jsonresult = array('error' =>  '0' , 'num_rows' =>  $num_rows,  'errmsg' =>  "Connections are fetched!" , 
			'results' =>  $allmembers  );   
		}
		else 
		{
			$jsonresult = array('error' =>  '0' , 'num_rows' => 0 ,  'errmsg' =>  "No connection exists!" , 
			'results' => null );  
		}
		return $jsonresult;  	  
	}
	
	public function get_status($data)
	{
		$source = $data['source'];   
		$target = $data['target'];
		
		$sql_query = "select status from mc_member_connections 
		where 
		(firstpartner=? and secondpartner=?) or (firstpartner=? and secondpartner=?) ";  
		$allstatus = $this->db->query($sql_query,array($source,$target,$target, $source) );  
		
		foreach($allstatus->result() as $item)
		{
			if($item->status == 1)
			{
				return 1;
			}
		}
		return 0;		
	} 
}

?>
