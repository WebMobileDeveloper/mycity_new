<?php

class MyInviteKnowLog extends CI_Model
{
	var $id ='';
	var $know_id=''; 
	var $partner_id='';  
	var $partner_name ='';
	var $know_name ='';
	var $hash_id = ''; 
	var $send_date=''; 
	var $join_date='';  
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$invitelogid=0;
		$this->db->select("count(*) as rcnt");
		$this->db->from("mc_invite_know_log"); 
		$this->db->where("hash_id", $data['hash_id'] ); 
		$result  = $this->db->get(); 
		if(	$result->num_rows() > 0)
		{
			$member_row = $result->row();
			if($member_row->rcnt == 0)
			{
				$ecntrs  = $this->db->query("select count(*) as ecnt from mc_invite_know_log 
				where partner_id='" . $data['partner_id'] . "' and know_name='" .  $data['know_name']   . "'");
				$suffix = $ecntrs->row()->ecnt; 
				if($suffix > 0)
				{
					$data['know_name'] = $data['know_name'] . "-" . ($suffix+1);
				}
				$this->db->insert("mc_invite_know_log", $data);
				$invitelogid = $this->db->insert_id() ;
			} 
		}
		return $invitelogid; 
	}
	
	
	public function update_log($data, $hash_id)
	{
		$this->db->where("hash_id",  $hash_id );
		$this->db->update("mc_invite_know_log", $data); 
	}
	
	function get_all( )
	{
		$sql_query = "select *, 
		(select username from mc_user where id = a.partner_id) as username, 
		(select client_name from user_people where id = a.know_id) as client_name 
		from mc_invite_know_log as a ";
		$result = $this->db->query( $sql_query  ); 
		return  $result ; 
	}
	
	function get_url_by_id( $id )
	{
		//get details 
		$sql_query = "select a.*, b.username  from mc_invite_know_log as a inner join mc_user as b on a.partner_id=b.id  where a.id='$id' ";
		$result = $this->db->query( $sql_query  ); 
		if($result->num_rows() > 0)
		{
			//getting know
			$row = $result->row();
			$know_name = $row->know_name;  
			$username =  implode( '-', array_filter(explode(' ', $row->username ) ) )   ;   
			return   strtolower( $username . "/" . $know_name ) ;
		}
		else 
		{
			return "";
		}
	}
	
	function get_invite_log($data)
	{
		$know_name = $data['know'];
		$partner_name = $data['partner']; 
		$sql_query = "select *, 
		(select username from mc_user where id = a.partner_id) as username, 
		(select client_name from user_people where id = a.know_id) as client_name 
		from mc_invite_know_log as a where know_name='$know_name' and partner_name='$partner_name' ";
		$result = $this->db->query( $sql_query  ); 
		return  $result ; 
	}
	
	
	function get_invite_log_by_hash($hash)
	{
		$sql_query = "select  a.know_name, a.partner_name,   a.partner_id, a.send_date, a.join_date, b.* 
		from mc_invite_know_log as a inner join user_people as b on 
		a.know_id=b.id where a.hash_id='$hash'  ";
		$result = $this->db->query( $sql_query  ); 
		return  $result ; 
	}
	 function get_invite_log_by_know($know_name)
	{
		$sql_query = "select  a.know_name, a.partner_name,   a.partner_id, a.send_date, a.join_date, b.* 
		from mc_invite_know_log as a inner join user_people as b on 
		a.know_id=b.id where a.know_name='$know_name'  ";
		$result = $this->db->query( $sql_query  ); 
		return  $result ; 
	}
	
	
}

?>