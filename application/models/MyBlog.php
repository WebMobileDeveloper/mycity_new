<?php

class MyBlog extends CI_Model
{
	var $id =''; 
	var $helptitle =''; 
	var $helptext =''; 
	var $helpvideo =''; 
	var $publish =''; 
	var $positionvar='';
	
	 
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("blog_posts", $data);
		$studentid = $this->db->insert_id() ; 
		return $studentid; 
	}
  
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('blog_posts'); 
	}
	
	function get_posts()
	{ 
		$this->db->select("*");
		$this->db->from("blog_posts"); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
	function get_single_post($id)
	{ 
		$this->db->select("*");
		$this->db->from("blog_posts"); 
		$this->db->where("id", $id);
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
}


?>