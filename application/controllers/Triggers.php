<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Triggers extends CI_Controller {

function __construct() {
        parent::__construct();
        $this->load->helper( array(  'url', 'cookie','form' ) );
		$this->load->library(array('session'  ) ); 
		$this->load->model('MyTriggers');
    }  
	public function index()
	{
		 if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		
		$uid = $this->session->id; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Reminders"; 
		 
		$this->load->model('Members'); 
		$pagedata['member']  = $this->Members->getprofile( $uid ); 
		$this->load->model('Helpbuttons');    
		$helpbuttons = $this->Helpbuttons->getbuttons( );
		
		
		$button_array = array();
		foreach($helpbuttons->result() as $row)
		{
			array_push($button_array,  array('id'=> $row->id, 
			'helptitle' => $row->helptitle, 
			'helpvideo' =>  $row-> helpvideo )  ); 
		}
		$pagedata['help_data_buttons']  = $button_array; 
		$remid =0;
		if( $this->input->post('btn_savetrigger') == 'save_trigger')
		{
			$triggerid = $this->input->post('triggerid');  
			$triggerName = $this->input->post('triggername');  
			$data = array(   'trigger_question' =>  $triggerName,
			'user_id' =>  $uid );
			$remid = $this->MyTriggers->add($data, $triggerid); 
			
			$this->session->set_userdata(array('tmsg'  => 'Changes are saved' ));
			redirect('/triggers', 'refresh'); 	
		} 
		
		$alltriggers = $this->MyTriggers->get_triggers($uid);
		$pagedata['alltriggers'] =$alltriggers ;
		$pagedata['remid'] = $remid;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata);
		$this->load->view('template/navigation_side',   $pagedata); 		
		$this->load->view('triggers/index', $pagedata);  
		$this->load->view('template/footer',   $pagedata);
	}
	  
	
}
