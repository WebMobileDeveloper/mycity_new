<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Program extends CI_Controller 
{
	function __construct() {
        parent::__construct();
        $this->load->helper('form');
		$this->load->helper('url');
		$this->load->library(  array( 'session', 'pagination' ) );
		
		
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
		$pagedata['title']  = "Add/Edit Member"; 
		
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
		$this->load->model('Vocations');  
		$pagedata['vocations']  = $this->Vocations->get_vocations( );
		
		$this->load->model('Lifestyles');  
		$pagedata['lifestyles']  = $this->Lifestyles->getlifestyles( );
		
		$this->load->model('Groups');  
		$pagedata['groups']  = $this->Groups->getgroups( ); 
		$this->load->model('Questions');  
		$pagedata['questions']  = $this->Questions->getquestions( );
		$this->load->model('Tags');  
		$pagedata['tags']  = $this->Tags->gettags( );
		
		if($this->session->role == 'user')
		{
			$this->load->model('Knows');
			$knows = $this->Knows->get_myknows($uid, 0, 10 );
			$pagedata['knows']  = $knows ;  
		}
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('know/index',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	
	public function question()
	{ 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		if(  $this->session->role != 'admin' )
		{
			redirect('/dashboard', 'refresh'); 
		} 
		
		$this->load->model('Programquestion');
		$uid = $this->session->id; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Program Question";  
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
		 
		if($this->input->post("btnsavequest"))
		{
			$data = array('program_id' => $this->input->post("programname") , 
			'question'=>  $this->input->post("tbquest"));
			
			if($this->input->post('qno') > 0)
			{
				$this->Programquestion->update($data, $this->input->post('qno') );  
				$this->session->set_userdata('msg_error', 'Program question save!');
				redirect( current_url() , 'refresh'); 
			}
			else 
			{
				$this->Programquestion->add($data); 
				$this->session->set_userdata('msg_error', 'Program question save!');
				redirect($this->config->item('base_url') . 'program/question', 'refresh'); 
			} 
		}
		
		
		if($this->input->post("btn_delquestion") =='rem')
		{
			$this->Programquestion->remove( $this->input->post("quesid") );
			redirect($this->config->item('base_url') . 'program/question', 'refresh'); 
		}
		
		$editid =  ( intval($this->uri->segment(4)) ?  $this->uri->segment(4) : 0 ) ;
		$mode =  (  $this->uri->segment(3) != '' ?  $this->uri->segment(3) : 0 ) ;
		
		$pagedata['allqs'] =null;
		if($mode == 'change' && $editid > 0)
		{
			$question_edit = $this->Programquestion->getquestion($editid );  
			$pagedata['question_edit'] = $question_edit;
		}
		
		$pagedata['allqs'] = $this->Programquestion->getall( ); 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('program/question',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	
	
	public function relations()
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
		$pagedata['title']  = "3 Touch Program Relation";  
		$this->load->model('Members'); 
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
				'helpvideo' =>  $row-> helpvideo 
				)
			);
		}
		 
		if($this->uri->segment(3) == '' || $this->uri->segment(3) =='timeline' )
		{
			$offset=0; 
		}
		else 
		{
			if($this->uri->segment(3)   == 'search' )
			{
				if($this->uri->segment(4) == ''  )
				{
					$offset=0;
				}
				else 
				{
					$offset= $this->uri->segment(4);
				} 
			}
			else 
			{
				$offset= $this->uri->segment(3) ; 
			}
		}
		
		if($this->input->get('c') == '1')
		{
			$this->session->unset_userdata('keyword');
			$this->session->unset_userdata('tags');  
		}
		
		$pagedata['help_data_buttons']  = $button_array; 
		$this->load->model('Tags');  
		$pagedata['tags']  = $this->Tags->gettags( );
		$this->load->model('Programparticipant');
		
		$pagedata['participants']  = $this->Programparticipant->get_participants(array('mid'=>$uid, 'program'=>1) ); 
		$this->load->model('Knows');
		
		if($this->input->post('search_know') == 'search')
		{
			$keyword = $this->input->post('tb_3trelation');
			$keytags =   $this->input->post('3tsearchtag') ;  
			$search_key = array('keyword'=> $keyword , 'tags' => $keytags ); 
			$this->session->set_userdata($search_key); 
			$pagedata['allknows']= $this->Knows->get_myknows_program(  $uid , $offset , 10, 1, 'not in',  $keyword, $keytags );
		
		}
		else 
		{
			if( $this->session->has_userdata('keyword'))
			{
				$pagedata['allknows']= $this->Knows->get_myknows_program(  $uid , $offset , 
				10, 1, 'not in', $this->session->keyword  ,  
				$this->session->tags   );
		
			}
			else 
			{
				$pagedata['allknows']= $this->Knows->get_myknows_program(  $uid , $offset , 10, 1 );
			}
		}
		
		
		$pagedata['relationid'] = 0;
		if(  $this->uri->segment(4) != '' )
		{  
			$querydata = array();
			$querydata['relid'] = $this->uri->segment(4);
			$querydata['mid'] = $uid ;
			$querydata['pid'] =  1; 
			$timelinelog= $this->Programparticipant->relationtimeline( $querydata ); 
			$pagedata['timeline']=$timelinelog;
			$pagedata['relationid'] =$this->uri->segment(4);  
		} 
		
		$pager_config['per_page'] =10; 
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
		$this->load->view('program/managerelationship',   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	 
	
	public function activities()
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
		$pagedata['title']  = "Program Activities";
		
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
		$this->load->model('Vocations');  
		$pagedata['vocations']  = $this->Vocations->get_vocations( );
		
		$this->load->model("Programparticipantanswer");
		$activities = $this->Programparticipantanswer->activity_log(array('pid' => 1 , 'mid' => $uid ));
		
		$pagedata['activities'] = $activities;
		$pagedata['pid'] = 1;
		
		 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('program/activity_progress',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	
}
