<?php

class MyPackages extends CI_Model
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
		$this->db->insert("packages", $data);
		$studentid = $this->db->insert_id() ; 
		return $studentid; 
	}
 

	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('packages'); 
	}
	
	function get_packages( )
	{
		$this->db->select("*");
		$this->db->from("packages");  
		$this->db->where("pkg_status", 'activate');
		$this->db->where("package_title <> 'Invite'");
		$packages  = $this->db->get(); 
		
		$this->db->select("pkg_id, services");
		$this->db->from("package_services");   
		$this->db->order_by("id");
		$package_services  = $this->db->get(); 
		 
		return array('packages' => $packages, 'pkg_services' => $package_services) ; 
	}
}

?>