<?php

class Citygeolocation extends CI_Model
{
	var $id ='';
	var $latitude='';
	var $longitude='';
	var $zip='';
	var $city =''; 
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("mc_city_geolocation", $data);
		$studentid = $this->db->insert_id() ;
		return $studentid; 
	}
	
	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('mc_city_geolocation'); 
	}
	
	function getknow($member_id)
	{ 
		$this->db->select("*");
		$this->db->from("mc_city_geolocation"); 
		$result  = $this->db->get(); 
		return  $result ; 
	}
}
 
?>