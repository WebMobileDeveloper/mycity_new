<?php

class MyReminders extends CI_Model
{
	var $id =''; 
	var $type=''; 
	var $subject=''; 
	var $reminderbody=''; 
	var $assignedto=''; 
	var $emailreminderon=''; 
	var $entrydate=''; 
	var $lastupdate=''; 
	var $enteredby=''; 
	var $isread=''; 
	var $isalertedvar ='';  
	  
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	 
	public function add($data)
	{
		$this->db->insert("mc_reminder", $data);
		$reminder_id = $this->db->insert_id() ; 
		return $reminder_id; 
	}
	
	public function update($data, $remid)
	{
		$this->db->where("id",  $remid);
		$this->db->update("mc_reminder", $data); 
	}
  
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('user_rating'); 
	}
	
	function get_my_reminders($userid)
	{
		if($userid == 1)
			$sql_query = " select * from mc_reminder where  enteredby= '$userid' order by entrydate desc"  ;  
		else 
			$sql_query = " select * from mc_reminder where  enteredby= '$userid' or assignedto= '$userid' order by entrydate desc"  ;  
		
		$rst = $this->db->query($sql_query);
		return  ($rst) ; 
	}
	
	function get_reminders($userid)
	{
		 
		$sql_query = " select * from mc_reminder where  assignedto= '$userid'" ;  
		$rst = $this->db->query($sql_query); 
		if($rst->num_rows() > 0 )
	    {
			$jsonresult[]  = array('error2' =>  '0' ,  'errmsg' =>  'Reminders fetched successfully!' , 
			'resultset2' =>$rst  ); 
		}
		else
		{
			$jsonresult[]  = array('error2' =>  '100' ,  'errmsg' =>  'No reminder found!' );
		}
		
		return  ($jsonresult) ; 
	}
	
	function get_reminder($id)
	{
		$sql_query = " select * from mc_reminder where id= '$id'" ;  
		$rst = $this->db->query($sql_query); 
		if($rst->num_rows() > 0 )
	    {
			$jsonresult[]  = array('error' =>  '0' ,  'errmsg' => 'Reminders fetched successfully!' , 
			'result' => $rst  ); 
		}
		else
		{
			$jsonresult[]  = array('error' =>  '1' ,  'errmsg' =>  'No reminder found!', 'result' => '' );
		}
		
		return  ($jsonresult) ; 
	}
	
	
	public function get_all_reminders($data)
	{
		$userid =  $data['userid'];
		$offset = $data['offset'];
		$size = $data['size'];
		 
		
		$sql_query = "select id, type,subject, reminderbody,  assignedto, 
		emailreminderon,  entrydate,  lastupdate, enteredby from mc_reminder 
		where enteredby='$userid' order by entrydate limit $offset, $size";
		$results = $this->db->query($sql_query);
		
		$result_count=0;
		$sql_query_count = "select count(*) as reccnt   from mc_reminder where enteredby='$userid' order by entrydate";
		$rst_count = $this->db->query($sql_query_count); 
		if($rst_count->num_rows() > 0)
			$result_count = $rst_count->row()->reccnt; 
	 
		$jsonresult = array('error' =>  '0' ,  'result' => $results, 'num_rows' => $result_count )   ;
		return $jsonresult;  
	}
	
	
	public function get_reminder_counts($userid)
	{
		if($userid == 1)
			$sql_query = " select * from mc_reminder where  enteredby= '$userid' order by entrydate desc"  ;  
		else
			$sql_query = " select * from mc_reminder where  enteredby= '$userid' or assignedto= '$userid' order by entrydate desc"  ;  
		
		$rst = $this->db->query($sql_query);
		if($rst->num_rows() > 0 )
		{
			$jsonresult[]  = array('error1' =>  '0' ,  'errmsg' =>  'Reminders fetched successfully!' , 
			'resultset1' => $rst  );  
		} 
		else
		{
			$jsonresult[] = array('error1' =>  '10' ,  'errmsg' =>  'No reminder found!', 
			'resultset1' => NULL );
		}

			$sql_query = " select * from mc_reminder where  assignedto= '$userid'" ;  
			$rst = $this->db->query($sql_query); 
		   if($rst->num_rows() > 0 )
		   {
			   $jsonresult[]  = array('error2' =>  '0' ,  
			   'errmsg' =>  'Reminders fetched successfully!' , 
				'resultset2' =>  $rst ); 
		   } 
		   else
		   {
			   $jsonresult[]  = array('error2' =>  '100' ,  'errmsg' =>  'No reminder found!', 
			   'resultset1' => NULL			   );
		   }
		   
		   
		return $jsonresult; 
	} 
}


?>