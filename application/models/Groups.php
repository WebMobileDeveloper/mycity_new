<?php

class Groups extends CI_Model
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
	public function update($data, $id)
	{
		$this->db->where("id", $id);
		$this->db->update("groups", $data);  
		return $this->db->affected_rows() > 0;
	} 

	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('groups'); 
	}
	
	function getgroups( )
	{ 
		$this->db->select("*");
		$this->db->from("groups"); 
		$this->db->order_by("grp_name");
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
	
	function get_new_listing_request( )
	{
		$sql_query =  "select cg.* , u.username    from  groups as cg   inner join mc_user as u on cg.request_by=u.id 
		 order by grp_name " ; 
		$result = $this->db->query($sql_query ); 
		return  $result ; 
	}
	
	
	
	
} 

?>