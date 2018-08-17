<?php

class MyTestimonials extends CI_Model
{
	var $id ='';
	var $videolink =''; 
	var $summary =''; 
	var $printorder =''; 
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("mc_testimonial", $data);
		$testimonialid = $this->db->insert_id() ; 
		return $testimonialid; 
	}
	
	public function update_sort_order($ids)
	{
		$i=0;
		for($i=0; $i < sizeof($ids); $i++)
		{
			$sql_query =  "update mc_testimonial set printorder='" . ($i+1) . "'  where id='" . $ids[$i]  . "'" ;  
			$this->db->query( $sql_query );
		}
		return $i; 
	}
	
	public function update($data, $id)
	{
		$this->db->where("id", $id);
		$this->db->update("mc_testimonial", $data);   
		return $this->db->affected_rows() > 0;
	} 
	

	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('mc_testimonial'); 
	}
	
	function get_video_testimonials( )
	{ 
		$this->db->select("*");
		$this->db->from("mc_testimonial"); 
		$this->db->order_by("printorder");
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
	function get_video_testimonial_by_id($id)
	{ 
		$this->db->select("*");
		$this->db->from("mc_testimonial"); 
		$this->db->where("id", $id);
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
	
}

?>