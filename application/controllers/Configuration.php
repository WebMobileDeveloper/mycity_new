<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configuration extends CI_Controller 
{
	function __construct() {
        parent::__construct();
        $this->load->helper(  'form'  ); 
		$this->load->helper(   'url'  );  
		$this->load->library('session');  
		$this->load->helper('cookie');
    }
	
	public function index()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		
		$uid = $this->session->id; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Configuration"; 
		 
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
		
		if( $this->input->post('btn_save_privacy') == 'save_privacy' )
		{
			$data = array( 'privacyoption'=> $this->input->post('config_privacy') );
			$this->load->model('Members');
			$this->Members->update($data, $uid);
		}
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata);
		$this->load->view('template/navigation_side',   $pagedata); 		
		$this->load->view('config/index', $pagedata);  
		$this->load->view('template/footer',   $pagedata);
		
	} 
	
	
	
	public function tagline()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		
		$uid = $this->session->id; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Configuration"; 
		
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
		
		if( $this->input->post('save_tagline') == 'save' )
		{
			$data = array('page_name' => 'tagline'  , 'page_title' => ' ' ,  'page_content'=> $this->input->post('tagline') ); 
			$this->Pagedata->add( $data );
			$this->session->set_userdata("msg_error", "Tagline saved!");
			redirect(current_url() , 'refresh'); 
		} 
		$page_data =$this->Pagedata->get_page_data( 'tagline' );
		$pagedata['page_data'] = $page_data; 
		$tagline ='';
		$pagedata['cur_url'] = 'tagline';
		$pagedata['tagline'] = $tagline;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata);
		$this->load->view('template/navigation_side',   $pagedata); 		
		$this->load->view('config/tagline', $pagedata);  
		$this->load->view('template/footer',   $pagedata); 	
	}
	
}

 
