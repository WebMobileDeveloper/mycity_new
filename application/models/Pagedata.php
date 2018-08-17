<?php

class Pagedata extends CI_Model
{
	var $id ='';
	var $page_name=''; 
	var $page_title=''; 
	var $page_content='';
	  
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$page_name = $data['page_name'];
		$this->db->select("count(*) as cnt");
		$this->db->from("pages_data"); 
		$this->db->where("page_name",  $page_name  ); 
		$result  = $this->db->get(); 
		$row = $result->row();
		if($row->cnt == 0)
		{
			$this->db->insert("pages_data", $data);
			$insertid = $this->db->insert_id() ; 
			return $insertid; 
		}
		else
		{
			$this->db->where("page_name",  $page_name  ); 
			$this->db->update("pages_data",  array('page_title' => $data['page_title'],  'page_content' => $data['page_content'] )   ); 
			return $this->db->affected_rows() > 0;
		} 
		  
	} 
	public function update($data, $id)
	{
		$this->db->where("id", $id);
		$this->db->update("pages_data", $data); 
		return $this->db->affected_rows() > 0; 
	}
	 
  
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('pages_data'); 
	}
	
	function get_page_data($page_name)
	{ 
		$this->db->select("*");
		$this->db->from("pages_data"); 
		$this->db->where("page_name", $page_name);
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
	
	function get_by_name($page_name)
	{ 
		$this->db->select("*");
		$this->db->from("pages_data"); 
		$this->db->where("page_name", $page_name);
		$this->db->order_by("id", "desc");
		$result  = $this->db->get(); 
		return  $result ; 
	}
}


?>