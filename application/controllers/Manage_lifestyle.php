<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_lifestyle extends CI_Controller 
{
	function __construct() 
	{
        parent::__construct();
        
		$this->load->helper( array('form', 'url', 'email', 'cookie' ) );
		$this->load->library(array('session', 'form_validation' ) );  
    }
	
	public function index()
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
		$this->load->model("Members");
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Lifestyle"; 
		$pagedata['member']  = $this->Members->getprofile( $uid ); 
		$this->load->model('Helpbuttons');    
		$helpbuttons = $this->Helpbuttons->getbuttons( ); 
		$button_array = array();
		foreach($helpbuttons->result() as $row)
		{
			array_push($button_array,  
				array( 
					'id'=> $row->id, 
					'helptitle' => $row->helptitle, 
					'helpvideo' =>  $row-> helpvideo )  ); 
		}
		$pagedata['help_data_buttons']  = $button_array;
		$this->load->model('Lifestyles');  
		if($this->input->post("btn_save") == 'update')
		{
			$this->session->set_userdata('msg_error', 'Lifestyle save!');
			$data = array( 'ls_name' => $this->input->post("tb_ls_name") );
			$this->Lifestyles->update($data, $this->input->post("hid_ls_id") ) ;
			redirect( current_url() , 'refresh');
		} 
		if($this->input->post("btn_save") == 'save')
		{
			$data = array( 'ls_name' => $this->input->post("tb_ls_name") );
			$lsid = $this->Lifestyles->add($data  ) ;
			if($lsid> 0)
			$this->session->set_userdata('msg_error_add', 'Lifestyle save!');
			else
			$this->session->set_userdata('msg_error_add', 'Lifestyle exists!'); 
			redirect(current_url(), 'refresh');
		} 
		$pagedata['lifestyles']  = $this->Lifestyles->getlifestyles( );
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('lifestyle/index',   $pagedata);
		$this->load->view('template/footer',   $pagedata);
	} 
}
	
?>