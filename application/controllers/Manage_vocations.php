<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_vocations extends CI_Controller 
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
		$pagedata['title']  = "Vocations"; 
		 
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
		$this->load->model('Vocations');  
		
		if($this->input->post("btn_save") == 'update')
		{
			$this->session->set_userdata('msg_error', 'Vocation save!');
			$data = array( 'voc_name' => $this->input->post("tb_voc_name") );
			$this->Vocations->update($data, $this->input->post("hid_voc_id") ) ;
			redirect( current_url() , 'refresh');
		} 
		if($this->input->post("btn_save") == 'save')
		{
			
			$data = array( 'voc_name' => $this->input->post("tb_voc_name") );
			$vocid = $this->Vocations->add($data  ) ;
			if($vocid> 0)
			$this->session->set_userdata('msg_error_add', 'Vocation save!');
			else
			$this->session->set_userdata('msg_error_add', 'Vocation exists!');
			
			redirect(current_url(), 'refresh');
		} 
		$pagedata['vocations']  = $this->Vocations->get_vocations( );   
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('vocations/index',   $pagedata);
		$this->load->view('template/footer',   $pagedata);
	}
	
	public function common_vocation()
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
		$this->load->model("MyCommonVocations");
		
		
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Vocations"; 
		 
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
		$this->load->model('Vocations');   
		$pagedata['vocations']  = $this->Vocations->get_vocations( );   
		
		
		if($this->input->post('save_com_voc') =='save')
		{
			$vocation= $this->input->post("member_voc");
			$common_voc  =implode(',',  $this->input->post("common_vocations") )  ;
			$data = array( 'member_voc' => $vocation, 'know_common_voc' => $common_voc );
			$savecnt= $this->MyCommonVocations->add($data);
			
			
			if($savecnt > 0)
			{
				$this->session->set_userdata("msg_error", "Common vocations saved!");
			}
			else
			{
				$this->session->set_userdata("msg_error", "Common vocations could not be saved!");
			}
			
			redirect( current_url() , 'refresh'); 
		} 
		
		
		$edit_id =  ( intval($this->uri->segment(4)) ?  $this->uri->segment(4) : 0 ) ;
		$mode =  (  $this->uri->segment(3)  ?  $this->uri->segment(3) : 0 ) ;
		$pagedata['edit_comvoc'] = null;
		if($mode =='change' && $edit_id > 0)
		{
			$edit_comvoc= $this->MyCommonVocations->get_common_vocation_by_id($edit_id); 
			$pagedata['edit_comvoc'] = $edit_comvoc;  
			
		}
		 
		$pagedata['all_comvocs'] = $this->MyCommonVocations->get_all(); 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('vocations/common_vocation',   $pagedata);
		$this->load->view('template/footer',   $pagedata);
	}
}
	
?>