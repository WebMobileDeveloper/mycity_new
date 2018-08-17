<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller 
{
	function __construct() 
	{
		parent::__construct();
        $this->load->helper(  'form'  ); 
		$this->load->helper(   'url'  );  
		$this->load->library('session');  
		$this->load->helper('cookie');
    }
	
	public function index()
	{
		$log_err='';
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Login"; 
		$rememberme=0; 
		$pid = $this->input->get("p"); 
		$pagedata['partnerid'] = $pid; 
		
		$this->load->model('Members');
		if($this->input->post('btnlogin') == "submit")
		{
			if( $this->input->post('remember_me')  == "on")
			{
				$rememberme=1;
			} 
			$data = array('email' => $this->input->post('username') , 'password' =>  $this->input->post('password') , 
			'rememberme' => $rememberme );
			$loginprofile = $this->Members->login( $data );
			$pagedata['login']  = $loginprofile;
			 
			if($loginprofile['id'] != 0)
			{
				
				if( $loginprofile['role'] == 'admin')
				{
					//this for admin folder temporary
					$_SESSION['email'] =  $this->input->post('username');
					$_SESSION['password'] =  $this->input->post('password');
				}  
				
				$this->session->set_userdata( $loginprofile ); 
				$cookie = array(
				'name'   => '_mcu',
				'value'  => json_encode( $loginprofile ) ,
				'expire' => time()+86500, 
				'path'   => '/' 
				); 
				$this->input->set_cookie(   $cookie  );
				
				$cookie = array(
				'name'   => '_mcu',
				'value'  => json_encode( $loginprofile ) ,
				'expire' => time()+86500, 
				'path'   => '/admin' 
				); 
				$this->input->set_cookie(   $cookie  );
				 
				$log_err= ''; 
				$pid = $this->input->post("partnerid");
				$pid_sess = $this->session->signup_partner_id;
				
				
				if($pid !=0 && $pid == $pid_sess)
				{
					$shortcode = $this->Members->get_shortcode($pid );
					
					if($shortcode !='')
					{
						redirect('/profile/' . $shortcode, 'refresh'); 
					}
					else 
					{
						redirect('/profile/' . $pid, 'refresh'); 
					} 
				}
				else 
				{
					redirect('/dashboard', 'refresh'); 
				}
			}
			else 
			{
				$log_err = 'Email or password is not correct.';
			}
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
				$loginlogrs = $this->Members->get_profile_by_login_log( $logintoken   );
				
				if($loginlogrs->num_rows() == 1 ) 
				{
					$loginprofile = $this->Members->login_from_session( 
					array(  
					'id' => $loginlogrs->row()->id,
					'rememberme' => 1 )
					);  
					$this->session->set_userdata( $loginprofile ); 
					 redirect('/dashboard', 'refresh'); 
				} 
			} 
		}  
		$pagedata['log_err'] = $log_err; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata);  
		$this->load->view('login'); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	function switch_user()
	{
	 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id;
		
		$switcher='off';  
		if ( !is_null( $this->input->cookie('_mcu') )) 
		{
			$mcu =  json_decode( "[" .     $this->input->cookie('_mcu')  . "]", true ) ;
			$switcher = $mcu[0]["switcher"] ; 
		}  
		if( $switcher == 'on' &&  $this->session->switcher == 'on')
		{
			$memberid = 1 ;
			$this->load->model('Members');
			$loginlogrs = $this->Members->getprofile( $memberid ); 
			if($loginlogrs->num_rows() == 1 ) 
			{
				//clear previous cookie and session
				delete_cookie('_mcu');  
				$loginprofile = $this->Members->login_from_session( 
				array(   
					'id' => $loginlogrs->row()->id,
					'rememberme' => 1 ,
					'switcher' =>  'off' )
				);  
				$this->session->set_userdata( $loginprofile );    
					$cookie = array(
					'name'   => '_mcu', 
					'value'  => json_encode( $loginprofile ) ,
					'expire' => time()+86500, 
					'path'   => '/' 
					); 
				$this->input->set_cookie(   $cookie  );
				$log_err= '';
				redirect('/dashboard', 'refresh');  
			} 
			else 
			{
				$log_err = 'Account switching failed!';
			}
		}
		else 
		{
			redirect('/dashboard', 'refresh');  
		}


		
		 
		   
	}
	
	
}

 
