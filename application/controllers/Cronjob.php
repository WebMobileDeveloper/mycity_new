<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cronjob  extends CI_Controller 
{
	function __construct() 
	{
        parent::__construct();
        $this->load->helper( array('form', 'cookie',  'url')); 
		$this->load->library(  array( 'session', 'pagination' ) );
		$this->load->model('Members');    
    } 
	
	public function index()
	{
		$this->load->model('Members'); 
		$this->load->model('Knows'); 
		$this->load->model('MyLoginLog'); 
		$mid = $this->MyLoginLog->get_todays_log(); 
		foreach($mid->result() as $row)
		{
			$referrals =$this->Knows->do_referral_mapping( $row->userid  , 'Mi'); 
		} 
	} 
	public function referral_map_rank_update()
	{
		$this->load->model('Members');  
		$this->load->model('Knows');  
		$mid = $this->Members->get_all_users(); 
		foreach($mid->result() as $row)
		{
			$this->Knows->rank_updater( $row->id  ); 
		} 
	}
	 
	/*
	public function auto_add_connection()
	{
		$this->load->model('Members');  
		$mid = $this->Members->get_all_users_with_name(); 
		foreach($mid->result() as $row)
		{
			$connect_data = array(  
			'partnerid'=> $row->id , 
			'user_id'=> '19'  );
			$this->Members->request_connection_in_db( $connect_data ); 
			echo "Done: " .$row->id . "<br/>";
		} 
	}
	
	*/
	
	
	
}
