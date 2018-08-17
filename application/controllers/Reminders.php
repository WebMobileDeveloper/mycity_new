<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reminders extends CI_Controller 
{
	function __construct() {
        parent::__construct();
        $this->load->helper(  'form'  ); 
		$this->load->helper(   'url'  );    
		$this->load->helper('cookie');$this->load->library(array('session', 'pagination' ) );
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
		
		$this->load->model('MyReminders');
		$allreminders = $this->MyReminders->get_my_reminders($uid); 
		$pagedata['allreminders'] = $allreminders;
		
		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata);
		$this->load->view('template/navigation_side',   $pagedata); 		
		$this->load->view('reminder/index', $pagedata);  
		$this->load->view('template/footer',   $pagedata);
		
	} 
	 
	
	public function add()
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
		if( $this->input->post('btn_savereminder') == 'save_reminder')
		{
			$reminderdate =$this->input->post('remindermail_day'); 
			
			$dateparts= explode ('/', $reminderdate); 
			$hr =$this->input->post('rem_hour');
			$min =$this->input->post('rem_min');
			$hrformat =$this->input->post('rem_format');
			$reminderdatetime='';
			if( sizeof($dateparts) == 3)
			{
				$reminderdatetime = date('Y-m-d H:i:s', strtotime($dateparts[2] . "-" . $dateparts[1] . "-" . $dateparts[0] . 
				" " . $hr . ":" . $min . " " . $hrformat) ); 
			}
			else 
			{
				$err = 1; 
				$errlog = array( 'err'  =>   '1', 'msg'  =>   'Invalid date provided!' ); 
			} 
			$this->load->model('MyReminders');
			$data = array( 
			'subject' =>  $this->input->post('rem_title'),
			'type' =>  $this->input->post("type"),
			'reminderbody' =>  $this->input->post("rem_text"),
			'assignedto' =>  $this->input->post('hid_assignno'),
			'emailreminderon' => $reminderdatetime,
			'enteredby' => $uid );
			$remid = $this->MyReminders->add($data);   
		} 
		$pagedata['remid'] = $remid;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata);
		$this->load->view('template/navigation_side',   $pagedata); 		
		$this->load->view('reminder/add', $pagedata);  
		$this->load->view('template/footer',   $pagedata); 
	}
	
	
	public function manage()
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
		 
		$offset =  ( intval($this->uri->segment(3)) ?  $this->uri->segment(3) : 0 ) ;
		  
		 
		$this->load->model('MyReminders');
		$searchdata = array('userid' =>  $uid,  'offset'=> $offset ,  'size'=>10);
		$allreminders = $this->MyReminders->get_all_reminders($searchdata); 
		$pagedata['allreminders'] = $allreminders; 
		  
		$pagedata['urlsegment'] = $offset;  
		$pager_config['per_page'] = 10; 
		$pager_config['full_tag_open'] = "<ul class='pagination'>";
		$pager_config['full_tag_close'] ="</ul>";
		$pager_config['num_tag_open'] = '<li>';
		$pager_config['num_tag_close'] = '</li>';
		$pager_config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		$pager_config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
		$pager_config['next_tag_open'] = "<li>";
		$pager_config['next_tagl_close'] = "</li>";
		$pager_config['prev_tag_open'] = "<li>";
		$pager_config['prev_tagl_close'] = "</li>";
		$pager_config['first_tag_open'] = "<li>";
		$pager_config['first_tagl_close'] = "</li>";
		$pager_config['last_tag_open'] = "<li>";
		$pager_config['last_tagl_close'] = "</li>"; 
		$pagedata['pager_config'] = $pager_config ;  
		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata);
		$this->load->view('template/navigation_side',   $pagedata); 		
		$this->load->view('reminder/manage', $pagedata);  
		$this->load->view('template/footer',   $pagedata); 
	}
	
	public function edit()
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
		$this->load->model('MyReminders');
		
		$button_array = array();
		foreach($helpbuttons->result() as $row)
		{
			array_push($button_array,  array('id'=> $row->id, 
			'helptitle' => $row->helptitle, 
			'helpvideo' =>  $row-> helpvideo )  ); 
		}
		$pagedata['help_data_buttons']  = $button_array; 
		$remid =0;
		if( $this->input->post('btn_savereminder') == 'save_reminder')
		{
			$editremid = $this->input->post('rem_id');  
			$reminderdate =$this->input->post('remindermail_day');  
			$dateparts= explode ('/', $reminderdate); 
			if(sizeof($dateparts)!=3)
			{
				$dateparts= explode ('-', $reminderdate); 
			}
			$hr =$this->input->post('rem_hour');
			$min =$this->input->post('rem_min');
			$hrformat =$this->input->post('rem_format');
			$reminderdatetime='';
			if( sizeof($dateparts) == 3)
			{
				$reminderdatetime = date('Y-m-d H:i:s', strtotime($dateparts[2] . "-" . $dateparts[1] . "-" . $dateparts[0] . 
				" " . $hr . ":" . $min . " " . $hrformat) ); 
			}
			else 
			{
				$err = 1; 
				$errlog = array( 'err'  =>   '1', 'msg'  =>   'Invalid date provided!' ); 
			}
		  
			$data = array( 
			'subject' =>  $this->input->post('rem_title'),
			'type' =>  $this->input->post("type"),
			'reminderbody' =>  $this->input->post("rem_text"), 
			'emailreminderon' => $reminderdatetime );
			$remid = $this->MyReminders->update($data, $editremid );   
		}  
		$editremid =  $this->uri->segment(3); 
		$reminder_det = $this->MyReminders->get_reminder($editremid); 
		$pagedata['reminder_det'] = $reminder_det;
		
		$pagedata['remid'] = $remid;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata);
		$this->load->view('template/navigation_side',   $pagedata); 		
		$this->load->view('reminder/edit', $pagedata);  
		$this->load->view('template/footer',   $pagedata); 
	}
	
	
	
	
}

 
