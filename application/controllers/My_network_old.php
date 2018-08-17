<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_network  extends CI_Controller 
{
	function __construct() {
        parent::__construct();
        $this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model( array(  'Members' ) ); 
    } 
	public function index()
	{ 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "View Partners";
		
		$pagedata['member']  = $this->Members->getprofile( $uid ); 
		$this->load->model('Helpbuttons');    
		$helpbuttons = $this->Helpbuttons->getbuttons( );
		
		$button_array = array();
		foreach($helpbuttons->result() as $row)
		{
			array_push($button_array,  array('id'=> $row->id,  
			'helptitle' => $row->helptitle, 
			'helpvideo' =>  $row-> helpvideo )  ); 
		}
		$pagedata['help_data_buttons']  = $button_array; 
		
		$this->load->model('Groups');  
		$pagedata['groups']  = $this->Groups->getgroups( );  
		 
		if($this->input->post("btn_view_profile") == "view_profile")
		{
			$member_id = $this->input->post('db_member');  
			$profile = $this->Members->getprofile( $member_id );
			$pagedata['profile'] = $profile ;
		}
		   
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('member/showpartner',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	
	
	public function highest_rated()
	{ 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "View Partners";
		
		$pagedata['member']  = $this->Members->getprofile( $uid ); 
		$this->load->model('Helpbuttons');    
		$helpbuttons = $this->Helpbuttons->getbuttons( );
		
		$button_array = array();
		foreach($helpbuttons->result() as $row)
		{
			array_push($button_array,  array('id'=> $row->id,  
			'helptitle' => $row->helptitle, 
			'helpvideo' =>  $row-> helpvideo )  ); 
		}
		$pagedata['help_data_buttons']  = $button_array; 
		
		$this->load->model('Groups');  
		$pagedata['groups']  = $this->Groups->getgroups( );  
		$this->load->model('Vocations');  
		$pagedata['vocations']  = $this->Vocations->get_vocations( );   
		if($this->input->post("btn_view_rated") == "view_rated")
		{
			$filter = array('size' => 10, 
			'userid'=> $uid, 
			'vocation'=> $this->input->post('db_voc'), 
			'group'=> $this->input->post('db_group'), 
			'goto'=> 1 ); 
			$profiles = $this->Members->get_rated_partners( $filter );
			$pagedata['profiles'] = $profiles ;
		}
			$this->load->view('template/head',   $pagedata); 
			$this->load->view('template/header',   $pagedata); 
			$this->load->view('template/common_header',   $pagedata); 
			$this->load->view('template/navigation_side',   $pagedata); 
			$this->load->view('member/highestrated',  $pagedata );
			$this->load->view('template/footer',   $pagedata);
		 
	}
	   
}
