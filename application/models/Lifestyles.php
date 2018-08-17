<?php

class Lifestyles extends CI_Model
{
	var $id =''; 
	var $voc_name=''; 
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
  
	public function add($data)
	{
		$ls_name = $data['ls_name'];
		$this->db->select("count(*) as cnt");
		$this->db->from("lifestyles"); 
		$this->db->where("ls_name",  $ls_name  ); 
		$result  = $this->db->get();
		
		$row = $result->row();
		if($row->cnt == 0)
		{
			$this->db->insert("lifestyles", $data);
			$lid = $this->db->insert_id() ; 
			return $lid;
		}
		else
		{
			return -1;
		}
	}
 
	public function update($data, $id)
	{
		$this->db->where("id", $id);
		$this->db->update("lifestyles", $data);  
		return $this->db->affected_rows() > 0;
	} 

	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('lifestyles'); 
	}
	
	function getlifestyles( )
	{ 
		$this->db->select("*");
		$this->db->from("lifestyles"); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
}

?>