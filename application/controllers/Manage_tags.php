<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_tags  extends CI_Controller 
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
		$pagedata['title']  = "Manage Tags"; 
		 
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
		$this->load->model('Tags');  
		
		if($this->input->post("btn_save") == 'update')
		{
			$this->session->set_userdata('msg_error', 'Tag save!');
			$data = array( 'tagname' => $this->input->post("tb_tag_name") );
			$this->Tags->update($data, $this->input->post("hid_tag_id") ) ;
			redirect( current_url() , 'refresh');
		} 
		if($this->input->post("btn_save") == 'save')
		{
			
			$data = array( 'tagname' => $this->input->post("tb_tag_name") );
			$tagid = $this->Tags->add($data  ) ;
			if($tagid> 0)
			$this->session->set_userdata('msg_error_add', 'Tag save!');
			else
			$this->session->set_userdata('msg_error_add', 'Tag exists!'); 
			redirect(current_url(), 'refresh');
		} 
		$pagedata['tags']  = $this->Tags->gettags( );   
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('tags/index',   $pagedata);
		$this->load->view('template/footer',   $pagedata);
	}
	
	 
}
	
?>