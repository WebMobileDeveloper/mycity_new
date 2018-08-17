<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_helpbutton extends CI_Controller 
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
		$this->load->model('Helpbuttons'); 
		$editid =  ( intval($this->uri->segment(3)) ?  $this->uri->segment(3) : 0 ) ;
		$mode =  (  $this->uri->segment(2) != '' ?  $this->uri->segment(2) : 0 ) ;
		$pagedata['helpbutton_edit'] = null;
		if($mode == 'change' && $editid > 0)
		{
			$helpbutton_edit = $this->Helpbuttons->get_button_by_id($editid );  
			$pagedata['helpbutton_edit'] = $helpbutton_edit;
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
		 
		
		if($this->input->post("btn_save") == 'save')
		{
			$id = $this->input->post("editid")	;
			$data = array( 'helptitle' => $this->input->post("helptitle"), 
			'helpvideo' => $this->input->post("helpvideo") );
			
			if($id>0)
			{
				$lsid = $this->Helpbuttons->update($data, $id  ) ;
				$this->session->set_userdata('msg_error', 'Help button information save!'); 
			}
			else
			{
				$lsid = $this->Helpbuttons->add($data  ) ;
				$this->session->set_userdata('msg_error', 'Help button information save!'); 
			}
			redirect(current_url(), 'refresh');
		} 
		$pagedata['helpbuttons']  = $this->Helpbuttons->getbuttons( );
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('helpbutton/index',   $pagedata);
		$this->load->view('template/footer',   $pagedata);
	}
	
}
	
?>