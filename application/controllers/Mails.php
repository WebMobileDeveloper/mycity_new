<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mails  extends CI_Controller 
{
	function __construct() {
        parent::__construct();
        $this->load->helper('form');
		$this->load->helper('url');
		$this->load->library(array('session', 'pagination' ) );
		 
    } 
	public function index()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id;  
	 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Inbox";
		
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
		
		$mailtype = $this->uri->segment(3); 
		if($mailtype == null)
		{
			$mailtype =0;
		}
		$offset = $this->uri->segment(4); 
		if($offset == null)
		{
			$offset = 0;
		}
		
		$pagedata['help_data_buttons']  = $button_array;
		
		$this->load->model('Mailbox'); 
		$this->load->model('Memberconnection'); 
		
		//remove mail if necessary 
		if( $this->input->post("mailid") >  0 )
		{
			$mailid= $this->input->post("mailid");
			$this->Mailbox->remove( $mailid);  
		}
		 
		
		$search_filter = array( 
		'receipent' => $this->session->email  ,
		'mailtype' => $mailtype ,  
		'offset' => $offset );
		
		$inbox = $this->Mailbox->get_inbox( $search_filter );
		if( $inbox['result']  != null ):
		foreach(   $inbox['result']->result() as $mitem)
		{
			$mitem->connect_status = $this->Memberconnection->get_status( array('source' => $this->session->id , 'target' => $mitem->partnerid )  );
		} 
		endif;
		
		$pagedata['mailtype'] = $mailtype;
		if( $this->input->post("btn_send_email") == 'send_email')
		{
			$data = array(
				'id'  =>   $this->input->post("receipentid") ,  
				'subject' =>   $this->input->post("membermailsubject"),
				'mailbody' =>  $this->input->post("mailbody") ,
				'username' =>  $this->session->name,
				'senderemail' =>  $this->session->email,
				'senderphone' =>   $this->session->phone 
			) ;
			$inbox = $this->Mailbox->send_mail_log($data);   
			redirect('/mails/inbox', 'refresh'); 
		} 
		$pagedata['inbox'] = $inbox; 
		 
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
		$this->load->view('mails/inbox',  $pagedata );
		$this->load->view('common/mail_composer',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	
	}
	  
	public function outbox()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Outbox";
		
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
		$this->load->model('Mailbox');


		//remove mail if necessary 
		if( $this->input->post("mailid") >  0 )
		{
			$mailid= $this->input->post("mailid");
			$this->Mailbox->remove( $mailid);  
		}

		
		
		$mailtype = $this->uri->segment(3); 
		if($mailtype == null)
		{
			$mailtype =0;
		}
		$offset = $this->uri->segment(4); 
		if($offset == null)
		{
			$offset = 0;
		}
		  
		
		$inbox = $this->Mailbox->get_outbox( array( 
		'receipent' => $this->session->email  ,
		'mailtype' => $mailtype ,  
		'offset' => $offset ) );
		
		$this->load->model("Memberconnection");
		if( $inbox['result']  != null ):
		foreach(   $inbox['result']->result() as $mitem)
		{
			$mitem->connect_status = $this->Memberconnection->get_status( array('source' => $this->session->id , 'target' => $mitem->partnerid )  );
		} 
		endif;
		
		$pagedata['mailtype'] = $mailtype; 
		if( $this->input->post("btn_send_email") == 'send_email')
		{
			$data = array(   
				'id'  =>   $this->input->post("receipentid") , 
				'subject' =>   $this->input->post("membermailsubject"),
				'mailbody' =>  $this->input->post("mailbody") ,
				'username' =>  $this->session->name,
				'senderemail' =>  $this->session->email,
				'senderphone' =>   $this->session->phone 
			) ;
			$inbox = $this->Mailbox->send_mail_log($data); 
			redirect('/mails/inbox', 'refresh'); 
		} 
		$pagedata['inbox'] = $inbox;
		
		 
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
		$this->load->view('mails/outbox',  $pagedata );
		$this->load->view('common/mail_composer',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	
	
	
	public function read()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id; 
		
		if( $this->input->get('type')  )
		{
			$pagedata['type'] = $this->input->get('type') ; 
		}
		else 
		{
			redirect('/mails/inbox', 'refresh'); 
		}


		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Outbox"; 
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
		$this->load->model('Mailbox');
		
		
		$mailid = $this->input->get("mail");
		$mail_to_read = $this->Mailbox->get_mail( $mailid  );
		$this->Mailbox->update( array('emailstatus' =>  1),  $mailid  );
		
		
		$this->load->model("Memberconnection"); 
		$pagedata['mail_details'] = $mail_to_read;  
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('mails/reader',  $pagedata );
		$this->load->view('common/mail_composer',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
}
