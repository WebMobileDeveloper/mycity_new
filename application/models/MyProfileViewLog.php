<?php 
class MyProfileViewLog  extends CI_Model
{
	var $id =''; 
	var $user_id=''; 
	var $ip=''; 
	var $viewed_on='';  
	var $viewed_by =''; 
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	} 
	public function add($data)
	{
		 $this->db->insert("mc_profile_view_log", $data);
		 $logid = $this->db->insert_id() ; 
		 return $logid; 
	}  
	function get_logs($data )
	{
		$offset = $data['offset'];
		$query = "select a.*, b.username 
		from  mc_profile_view_log as a 
		inner join mc_user as b 
		on a.user_id=b.id 
		order by a.user_id asc, viewed_on desc limit $offset,10 ";
		$result  = $this->db->query( $query  );
		return  $result ; 
	}
	
	function get_logs_by_id($data )
	{
		$id = $data['id'];
		$offset = $data['offset'];
		$query = "select * from mc_user where id in 
		(select distinct user_id from  mc_profile_view_log where viewed_by='$id' ) 
		order by username limit $offset,10 ";
		$result  = $this->db->query( $query  );
		return  $result ; 
	} 
} 
?>