<?php
class MyCountry  extends CI_Model
{
	var $id =''; 
	var $name=''; 

	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	
	function get_all( )
	{ 
		$this->db->select("*");
		$this->db->from("mc_country"); 
		$this->db->order_by("name");
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
	
}

?>


