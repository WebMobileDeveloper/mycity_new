<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sign_up  extends CI_Controller 
{
	function __construct() 
	{
        parent::__construct(); 
		$this->load->helper( array('form', 'url', 'email', 'cookie' ) );
		$this->load->library(array('session', 'form_validation' ) );  
    } 
	
	public function index()
	{
		if( $this->session->has_userdata('id') )
		{
			redirect('/dashboard', 'refresh'); 
		}
		
		$base = $this->config->item('base_url');
		$pagedata['base'] =$base ;
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Join MyCity";  
		$pagedata['msg'] =''; 
		$pagedata['cname'] = '';
		$pagedata['cmail'] = '';
		
		$pid = $this->input->get("p");
		$pid_sess = $this->session->signup_partner_id;
		if($pid == $pid_sess)
		{
			$pagedata['partnerid'] = $pid;
		}
		 
		if($this->input->post("btn_save") == "save")
		{
			$user_data = array ('user_pass'=> md5($this->input->post('e_password') ),
			'user_email' => $this->input->post('e_email'), 
			'username' =>  $this->input->post('e_name'),
			'user_role' => 'user', 'createdOn' => date('Y-m-d H:i:s' ) ); 
			$this->load->model("Members");
			$memberid = $this->Members->add($user_data); 
			$this->load->model("Userdetails");
			$this->Userdetails->save(
			array('user_id' => $memberid, 
			'createdOn' =>  date('Y-m-d H:i:s') ) );
			
			
			
			if($this->input->post('e_partnerid') == $pid_sess)
			{
				//update member connection
				/* 
				$this->load->model("Memberconnection");
				$this->Memberconnection->add(
					array(
					'firstpartner' => $memberid ,
					'secondpartner' => $pid_sess  ,
					'requestdate' => date('Y-m-d H:i:s'),
					'approvedon' => date('Y-m-d H:i:s') ,
					'status' => '1'
					)
				);
				*/
				
			} 
			//login 
			$rememberme=1;
			$data = array(
				'email' => $this->input->post('e_email')  , 
				'password' => $this->input->post('e_password')  , 
				'rememberme' => $rememberme );
			$loginprofile = $this->Members->login( $data ); 
			$pagedata['login']  = $loginprofile;
			
			if($loginprofile['id'] != 0)
			{
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
						
						
						$shortcode = $this->Members->get_shortcode( $pid_sess );
					
						if($shortcode !='')
						{
							redirect('/profile/' . $shortcode, 'refresh'); 
						}
						else 
						{
							redirect('/profile/' . $pid_sess, 'refresh'); 
						} 
					  
				}
				/* login ends */ 		
		}
		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('signup/index' , $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
}


?>