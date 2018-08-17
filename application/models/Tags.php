<?php

class Tags extends CI_Model
{
	var $id ='';
	var $tagname =''; 
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$tagname = $data['tagname'];
		$this->db->select("count(*) as cnt");
		$this->db->from("mc_tags"); 
		$this->db->where("tagname",  $tagname  ); 
		$result  = $this->db->get(); 
		$row = $result->row();
		if($row->cnt == 0)
		{
			$this->db->insert("mc_tags", $data);
			$tagid = $this->db->insert_id() ; 
			return $tagid; 
		}
		else
		{
			return -1;
		} 
	}
	
	public function update($data , $id)
	{
		$this->db->where("id", $id);
		$this->db->update("mc_tags", $data); 
		return $this->db->affected_rows() > 0;
	}
  
 
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('mc_tags'); 
	}
	
	function gettags( )
	{ 
		$this->db->select("*");
		$this->db->from("mc_tags"); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
}

?>