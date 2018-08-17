<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class City   extends CI_Controller 
{
	function __construct() {
        parent::__construct();
     $this->load->library(array('session', 'pagination' ) );
		$this->load->helper( array('form', 'url', 'email' , 'array') ); 
    } 
	
	public function index()
	{ 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		 if($this->session->role == 'user')
		{
			redirect('/dashboard', 'refresh'); 
		}
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Add/Edit Member";
		
		$this->load->model('Members'); 
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
		
		
		$this->load->model('Vocations');  
		$pagedata['vocations']  = $this->Vocations->get_vocations( ); 
		$this->load->model('Lifestyles');  
		$pagedata['lifestyles']  = $this->Lifestyles->getlifestyles( ); 
		$this->load->model('Groups');  
		$pagedata['groups']  = $this->Groups->getgroups( ); 
		$this->load->model('Questions');  
		$pagedata['questions']  = $this->Questions->getquestions( );
		$this->load->model('Tags');  
		$pagedata['tags']  = $this->Tags->gettags( );
		
		if($this->session->role == 'user')
		{
			$this->load->model('Knows');
			$knows = $this->Knows->get_myknows($uid, 0, 10 );
			$pagedata['knows']  = $knows ;
		}
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('know/index',  $pagedata );
		$this->load->view('template/footer',   $pagedata); 
	}
	 
	
	public function request_listing()
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
		$pagedata['title']  = "Add/Edit Member"; 
		$this->load->model('Members'); 
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
		
		
		if($this->input->post("btn_save") == "city_listing")
		{
			$city = $this->input->post("tbnewcityname");
			$this->load->model("Groups");
			$this->Groups->add(array('grp_name'=> $city , 'islisted' => 0, 'request_by'=> $uid ));
			redirect('/city/request_listing', 'refresh'); 
		}
		 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('city/request_listing',  $pagedata );
		$this->load->view('template/footer',   $pagedata); 
	} 
	 
}
