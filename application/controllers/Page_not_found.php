<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page_not_found extends CI_Controller {

function __construct() {
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
		$pagedata['title']  = "404";  
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('errors/page_missing' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata);
	}
	  
	
}
