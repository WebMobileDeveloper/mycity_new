<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_groups extends CI_Controller 
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
		$pagedata['title']  = "Manage Groups/Cities"; 
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
		$this->load->model('Groups');  
		if($this->input->post("btn_save") == 'update')
		{
			$this->session->set_userdata('msg_error', 'Group save!');
			$data = array( 'grp_name' => $this->input->post("tb_grp_name") );
			$this->Groups->update($data, $this->input->post("hid_grp_id") ) ;
			redirect( current_url() , 'refresh');
		} 
		if($this->input->post("btn_save") == 'save')
		{
			$data = array( 'grp_name' => $this->input->post("tb_grp_name") );
			$lsid = $this->Groups->add($data  ) ;
			if($lsid> 0)
			$this->session->set_userdata('msg_error_add', 'Group save!');
			else
			$this->session->set_userdata('msg_error_add', 'Group exists!'); 
			redirect(current_url(), 'refresh');
		} 
		$pagedata['groups']  = $this->Groups->getgroups( );
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('groups/index',   $pagedata);
		$this->load->view('template/footer',   $pagedata);
	} 
	
	
	
	public function new_listing_request()
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
		$pagedata['title']  = "Manage Groups/Cities"; 
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
		$this->load->model('Groups');   
		
		$edit_id =  ( intval($this->uri->segment(4)) ?  $this->uri->segment(4) : 0 ) ;
		$mode =  (  $this->uri->segment(3)  ?  $this->uri->segment(3) : 0 ) ;
		if($edit_id > 0)
		{
			if($mode=='add')
				$update_cnt= $this->Groups->update( array( 'islisted' =>  '1'  ), $edit_id );
			else  if($mode=='remove')
				$update_cnt=  $this->Groups->update( array( 'islisted' =>  '0'  ), $edit_id ); 
			
			if($update_cnt > 0)
			{
				$this->session->set_userdata('msg_error', 'City listing updated!');
			}
		}
		$pagedata['list_requests']  = $this->Groups->get_new_listing_request( );
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('groups/new_listing',   $pagedata);
		$this->load->view('template/footer',   $pagedata);
	} 
}
	
?>