<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

function __construct() {
        parent::__construct();
        $this->load->helper( array('form', 'url', 'email', 'cookie' ) );
		$this->load->library(array('session', 'form_validation' ) ); 
		  
    }
	
	 
	public function index()
	{
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "MyCity";
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		 
		
		if($this->input->post("reg_step1") == "signup" )
		{
			$this->form_validation->set_rules('email1', 'Email', 'required|valid_email' );
			$email = $this->input->post("email1");
			if ($this->form_validation->run() == FALSE)
			{
				$this->load->view('index'); 
			}
			else 
			{
				$data = array('email' =>  $email );
				$new_signup = array( 'new_signup' =>  $email );
				$this->session->set_userdata( $new_signup );  
				redirect('/register', 'refresh');  
			}  
		}  
		else 
		{
			$this->load->view('index');	
		}
		$this->load->view('template/footer',   $pagedata); 
		
	}
	  
	
}
