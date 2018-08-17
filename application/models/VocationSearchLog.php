<?php

class VocationSearchLog extends CI_Model
{
	var $code =''; 
	var $vocation=''; 
	var $location=''; 
	var $user_id=''; 
	var $created_at=''; 
	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		 $this->db->insert("vocation_search_logs", $data);
			$logid = $this->db->insert_id() ; 
			return $logid; 
	}  
	
	function get_logs($data )
	{
		$offset = $data['offset'];
		$query = "select vsl.*,mu.username from  vocation_search_logs as vsl 
		inner join mc_user as mu  on vsl.user_id=mu.id order by created_at desc limit $offset,10 ";
		$result  = $this->db->query( $query  );
		return  $result ; 
	}
}

?>