<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller 
{
	function __construct() 
	{
        parent::__construct();
        
		$this->load->helper( array('form', 'url', 'email', 'cookie' ) );
		$this->load->library(array('session', 'form_validation' ) ); 
		
    }
	
	 public function index()
	{
	 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "About MyCity";  
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('about' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata);
		
	}
	public function edit()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		
		$uid = $this->session->id; 
		
		if(  $this->session->role != 'admin'  )
		{
			redirect('/dashboard', 'refresh'); 
		}
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Edit About Text"; 
		
		$this->load->model('Pagedata');
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
		
		if( $this->input->post('save_about') == 'save' )
		{
			$data = array('page_name' => 'about'  , 'page_title' =>  $this->input->post('title')  ,  'page_content'=> $this->input->post('aboutcontent') ); 
			$this->Pagedata->add( $data );
			$this->session->set_userdata("msg_error", "About Page Saved!");
			redirect(current_url() , 'refresh'); 
		} 
		$page_data =$this->Pagedata->get_page_data( 'about' );
		$pagedata['page_data'] = $page_data; 
		$tagline ='';
		$pagedata['cur_url'] = 'aboutpage'; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata);
		$this->load->view('template/navigation_side',   $pagedata); 		
		$this->load->view('about/edit', $pagedata);  
		$this->load->view('template/footer',   $pagedata); 
	} 
}
	
?>