<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Join_My_City extends CI_Controller 
{
	function __construct() 
	{
        parent::__construct();
        
		$this->load->helper( array('form', 'url', 'email', 'cookie' ) );
		$this->load->library(array('session', 'form_validation' ) ); 
		
    }
	
	 public function index()
	{
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
		if($this->input->post("btn_save") == "save")
		{
			$user_data = array ('user_pass'=> md5($this->input->post('e_password') ),
			'user_email' => $this->input->post('e_email'), 
			'username' =>  $this->input->post('e_name'),
			'user_role' => 'user', 'createdOn' => date('Y-m-d H:i:s' ) ); 
			$this->load->model("Members");
			$memberid = $this->Members->add($user_data); 
			
			if($memberid > 0 )
				$pagedata['msg'] = 'Your account has been created. Plase <a href="' . $base  . 'login">login</a> to proceed!';
			else 
				$pagedata['msg'] = 'A similar account exists with the same email! If you have forgotten password, use password recovery tool!';
			
		}
		else 
		{ 
			if($this->input->get('token') && $this->input->get('l') && $this->input->get('hval')  )
			{
				$token = $this->input->get('token');
				$tokenlength = $this->input->get('l');
				$tlhash = $this->input->get('hval');
				$id = substr($token, 0, $tokenlength);
				$hashid  = substr($token, $tokenlength, strlen($token)-1  );
			 
				$this->load->model("Knows");
				$knowprofile = $this->Knows->get_know_profile($id);
				$row = $knowprofile->row(); 
				$pagedata['cmail'] = $row->client_email; 
				$pagedata['cname'] = $row->client_name; 
				if(md5($id) !=  $hashid )
				{
					$pagedata['msg'] = 'Invalid access!';
				} 
			}
			else
			{
			   $pagedata['msg'] = 'Invalid access!';
			}
		}
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('member/claim_profile' , $pagedata); 
		$this->load->view('template/footer',   $pagedata);
		
	}
}


?>