<?php

class MyExcelImportLog extends CI_Model
{
	var $id =''; 
	var $user_id=''; 
	var $filepath=''; 
	var $last_row_processed=''; 
	var $status=''; 
	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$this->db->insert("mc_upload_log", $data);
		$logid = $this->db->insert_id() ; 
		return $logid; 
	} 
	
	public function get_files($status)
	{
		$this->db->select("*");
		$this->db->from("mc_upload_log");
		$this->db->where('status', $status );
		$this->db->order_by("id", "desc") ; 
		$result  = $this->db->get(); 
		return  $result ;
	} 
	  
	
	public function update_import_log($data)
	{  
		$this->db->where('filepath', $data['filepath']);
		$this->db->update('mc_upload_log', $data);
	}
	
	
	
	public function get_file_details($uid)
	{
		$this->db->select("*");
		$this->db->from("mc_upload_log");
		$this->db->where('user_id', $uid );
		$this->db->order_by("id", "desc") ; 
		$result  = $this->db->get(); 
		return  $result ;
	} 
	
	
}

?>