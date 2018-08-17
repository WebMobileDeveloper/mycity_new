<?php

class Userdetails  extends CI_Model
{
	var $id =''; 
	var $user_id=''; 
	var $street=''; 
	var $city=''; 
	var $zip=''; 
	var $current_company=''; 
	var $linkedin_profile=''; 
	var $country=''; 
	var $groups=''; 
	var $target_clients=''; 
	var $target_referral_partners=''; 
	var $vocations=''; 
	var $about_your_self=''; 
	var $upd_public_private=''; 
	var $upd_reminder_email=''; 
	var $createdOn=''; 
	var $lcid='';  
	
	  
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	
	public function save($data)
	{
		$insert_id=0;
		$this->db->select("count(*) as rcnt");
		$this->db->from("user_details"); 
		$this->db->where("user_id", $data['user_id'] );
		$result  = $this->db->get(); 
		if(	$result->num_rows() > 0)
		{
			$member_row = $result->row();
			if($member_row->rcnt == 0)
			{
				$this->db->insert("user_details", $data);
				$insert_id = $this->db->insert_id() ; 
			} 
		}
		return $insert_id;  
	}
	 
	
	public function update_vocation($data,$user_id)
	{
		$this->db->where("user_id",  $user_id );
		$this->db->update("user_details", $data); 
	}
	
	public function update($data,$user_id)
	{
		$insert_id=0;
		$this->db->select("count(*) as rcnt");
		$this->db->from("user_details"); 
		$this->db->where("user_id", $user_id );
		$result  = $this->db->get(); 
		if(	$result->num_rows() > 0)
		{
			$member_row = $result->row();
			if($member_row->rcnt == 0)
			{
				$data['user_id'] = $user_id;
				$this->db->insert("user_details", $data);
				$insert_id = $this->db->insert_id() ; 
			}
			else 
			{
				$this->db->where("user_id",  $user_id );
				$this->db->update("user_details", $data); 
			}	
		} 
	}
	 
	
}

?>