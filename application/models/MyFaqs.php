<?php

class MyFaqs extends CI_Model
{
	var $id ='';
	var $helptitle=''; 
	var $helptext=''; 
	var $helpvideo='';
	var $publish=''; 
	var $position='';
	var $helpurl='';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("helps", $data);
		$helpid = $this->db->insert_id() ; 
		return $helpid; 
	} 
	
	public function update($data, $id)
	{
		$this->db->where("id", $id);
		$this->db->update("helps", $data); 
		return $this->db->affected_rows() > 0;
	}
	
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('helps'); 
	}
	
	function get_faqs( )
	{ 
		$this->db->select("*");
		$this->db->from("helps"); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
	function get_faq_by_id($id)
	{
		$this->db->select("*");
		$this->db->from("helps"); 
		$this->db->where("id", $id);
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
	function get_faq_item($title)
	{ 
		$this->db->select("*");
		$this->db->from("helps"); 
		$result  = $this->db->get(); 
		return  $result ; 
	} 
}

?>