<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	function __construct() 
	{
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
		$pagedata['title']  = "";
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$pagedata['errmsg'] ='';
		 
		$this->load->model("Members");
		$this->load->model('MyStatement');
		$allstatements = $this->MyStatement->get_statements();
		$pagedata['allstatements'] = $allstatements;
		
		if($this->session->id)
		{
			redirect('/dashboard', 'refresh');
		} 
		
		if ( !is_null( get_cookie('_mcu') )) 
		{
			$mcu =  json_decode( "[" .     get_cookie('_mcu')  . "]", true ) ; 
			//get token
			$logintoken = $mcu[0]["token"];  
			  
			if($mcu[0]["role"] == 'admin')
				$this->session->set_userdata('logintoken',  $logintoken );
			 
			if($logintoken != '')
			{
				$switcher =  $mcu[0]["switcher"];  
				$this->load->model("Members");
				$loginlogrs = $this->Members->get_profile_by_login_log( $logintoken   ); 
				if($loginlogrs->num_rows() == 1 ) 
				{
					$loginprofile = $this->Members->login_from_session( array( 
					'id' => $mcu[0]['id'],
					'rememberme' => 1, 'switcher' => $switcher  ));
					
					$pagedata['login']  = $loginprofile;
					if($loginprofile['id'] != 0)
					{
						$this->session->set_userdata( $loginprofile );
						$cookie = array(
						'name'   => '_mcu',
						'value'  => json_encode( $loginprofile ) ,
						'expire' => time() +  86500, 
						'path'   => '/' 
						);
						$this->input->set_cookie(   $cookie  );
						 redirect('/dashboard', 'refresh'); 
					} 
				} 
			} 
		}
		if($this->input->post("reg_step1") == "signup" )
		{
			 
			$this->form_validation->set_rules('email1', 'Email', 'required|valid_email' );
			$email = $this->input->post("email1");
			if ($this->form_validation->run() == FALSE)
			{
				$this->load->view('index', $pagedata); 
			}
			else 
			{
				if($this->Members->check_duplicate($email) )
				{
					$pagedata['errmsg'] = 'Email you provided has an account already.';
					$this->load->view('index', $pagedata); 
				}
				else 
				{
					$data = array('email' =>  $email );
					$new_signup = array( 'new_signup' =>  $email );
					$this->session->set_userdata( $new_signup );  
					redirect('/register', 'refresh');  
				} 
			}  
		}  
		else 
		{
			$this->load->view('index', $pagedata);	
		}
		$this->load->view('template/footer',   $pagedata); 
		
	} 
}
