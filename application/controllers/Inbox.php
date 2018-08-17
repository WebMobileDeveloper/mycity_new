<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inbox extends CI_Controller 
{
	function __construct() {
        parent::__construct();
        $this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('session');
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
		$this->load->model('Mailbox');   
		$inbox = $this->Mailbox->get_inbox( array( 
		'receipent' => $this->session->email  ,
		'mailtype' => 0 ,  
		'page' => 1 ) );
		$pagedata['inbox'] = $inbox;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('mails/inbox',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	
	 
}
