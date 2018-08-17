<?php

class Vocations extends CI_Model
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
		$vocname = $data['voc_name'];
		$this->db->select("count(*) as cnt");
		$this->db->from("vocations"); 
		$this->db->where("voc_name",  $vocname  ); 
		$result  = $this->db->get();
		
		$row = $result->row();
		if($row->cnt == 0)
		{
			$this->db->insert("vocations", $data);
			$vocid = $this->db->insert_id() ; 
			return $vocid;
		}
		else
		{
			return -1;
		}
	}
 
	public function update($data, $id)
	{
		$this->db->where("id", $id);
		$this->db->update("vocations", $data);  
	} 

	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('vocations'); 
	}
	
	function get_vocations( )
	{ 
		$this->db->select("*");
		$this->db->from("vocations"); 
		$this->db->order_by("voc_name");
		$result  = $this->db->get(); 
		return  $result ; 
	}
}

?>