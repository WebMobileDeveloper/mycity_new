<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Business extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library(array('session', 'pagination' ) );
		$this->load->model( array('Knows', 'Members' ) );  
    }
	
	public function index()
	{
		 
		$view_name ='business/index';
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id;   	
		if(intval($this->uri->segment(3)))
		{
			$offset = $this->uri->segment(3) ;
			$this->session->set_userdata('tab1page', $offset);  
		}
		else
		{
			$offset =  ( $this->session->tab1page > 0 ? $this->session->tab1page : 0 ) ; 
		}
		
		if(intval($this->uri->segment(4)))
		{
			$offset2 = $this->uri->segment(4) ;
			$this->session->set_userdata('tab2page', $offset2);
		}
		else
		{
			$offset2 =  ($this->session->tab2page ? $this->session->tab2page : 0 ) ;
		} 
		$pagedata['offset'] =$offset; 
		$pagedata['offset2'] =$offset2;
		
		$this->load->model("MyGlobalSearchLog");
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['profile_img'] = $this->config->item('profile_img');
		$pagedata['site_path'] = $this->config->item('site_path'); 
		$pagedata['title']  = "Search Members"; 
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
		$iszip = ( is_int($this->input->post('gscityorzip'))  == true ? 1 : 0 );
		 
		if($this->input->post("connect_req") == "send")
		{
			$know_page = ( $this->input->post('page') ? $this->input->post('page') : 0 ); 
			$url =   $this->input->post('url')  ;
			 
			$connect_data = array(  
			'partnerid'=> $this->input->post('partnerid'),
			'useremail'=>  $this->input->post('useremail'),
			'user_id'=>  $uid ,
			'connect_req' => $this->input->post('connect_req') );
			$req_result = $this->Members->request_connection( $connect_data );
			$pagedata['req_result'] = $req_result ;
			$this->session->set_userdata("error_code", $req_result['error']); 
			$this->session->set_userdata("msg_error", $req_result['errmsg']); 
			if($url =='know')
				redirect('/business/search/knows/'  . $know_page , 'refresh'); 
			else			
				redirect('/business/search/' . $know_page , 'refresh'); 
		} 
		 
		 
		if($this->input->post("bcp") == "claim_profile")
		{ 
			$know_page = ( $this->input->post('page') ? $this->input->post('page') : 0 );
			$pc_data = array( 
			'knowid'=> $this->input->post('knowid'), 
			'email'=>  $this->input->post('email'),
			'user_id'=>  $uid ,
			'name' => $this->input->post('name') );  
			 $req_result = $this->Members->send_claim_profile_invite( $pc_data );
			   
			 $this->session->set_userdata("msg_error", $req_result['errmsg']);  
			 redirect('/business/search/knows/' . $know_page , 'refresh'); 	
		} 
		 
		if($this->input->post("btn_global_search") == "global_search" && 
		$this->input->post('gskey') != '')
		{ 
			$offset=0;
			$offset2=0;   
			$this->session->set_userdata('tab1page', 0);  
			$this->session->set_userdata('tab2page', 0);  
			$search_data = array( 
			'bs_search_key' => $this->input->post('gskey')  ,
			'bs_search_city' => $this->input->post('gscityorzip')  ,  
			'bs_search_vocation' => $this->input->post('gskey') ,
			'bs_search_page' => '1', 'bs_search_userid' => $uid ,
			'bs_search_utype'=> 1 ,
			'bs_search_iszip'=>$iszip ); 
			$this->session->set_userdata( $search_data ); 
			  
			$search_data =   array( 
			'keyword' => $this->input->post('gskey')  ,
			'city' => $this->input->post('gscityorzip')  ,  
			'vocation' => $this->input->post('gskey') ,
			'offset' => $offset , 
			'offset2' => $offset2,
			'userid' => $uid ,'utype'=> 1 ,'iszip'=>$iszip ); 
			$this->MyGlobalSearchLog->add(  array( 'user_id' =>  $uid , 'keyword' =>  $this->input->post('gskey'), 'city_zip' => $this->input->post('gscityorzip')  )  );
			
		}
		else 
		{
			$search_data =   array( 
			'keyword' => $this->session->bs_search_key  ,
			'city' => $this->session->bs_search_city  ,  
			'vocation' => $this->session->bs_search_vocation  ,
			'offset' => $offset,
			'offset2' => $offset2,
			'userid' => $this->session->bs_search_userid  , 
			'utype'=> $this->session->bs_search_utype  , 
			'iszip'=> $this->session->bs_search_iszip   );
			
			$this->MyGlobalSearchLog->add(  array( 'user_id' =>  $this->session->bs_search_userid  , 
			'keyword' =>  $this->session->bs_search_key  , 
			'city_zip' => $this->session->bs_search_city  )  );
			
			
			if(intval($this->uri->segment(4)))
			{
				$view_name ='business/member_knows';
			}
			else 
			{
				if($this->uri->segment(3) == 'knows')
				{
					$view_name ='business/member_knows'; 
				}
				else 
				{
					if($this->input->post("vu_know") != 1   )
					{
						$view_name ='business/index';  
					}
					else 
					{
						$view_name ='business/member_knows';
					} 
				}
			} 
		}
		 
		if( $this->input->post("btn_send_email") == 'send_email')
		{
			$this->load->model("Members");
			$receipent = $this->Members->getprofile($this->input->post('receipentid'));
			
			$receipent_email  = $receipent->row()->user_email ;
			$ds = DIRECTORY_SEPARATOR;  
			$path =  $this->config->item("site_path")  ;   
			if(  file_exists( $path . "templates/black_template_03.txt" ) )
			{
				$filecontent = file_get_contents( $path . "templates/black_template_03.txt" ) ;  
				$filecontent = str_replace("{mail_body}", $this->input->post("mailbody")  , $filecontent ) ;  
				$filecontent = str_replace("{partner}",  $receipent->row()->username  , $filecontent ) ;  
				$filecontent = str_replace("{salutation}",  $this->session->name  , $filecontent ) ;  
				$filecontent = str_replace("{year}", date('Y') , $filecontent ) ;  
				$mailbody  = $filecontent ; 
			} 
			send_email( $receipent_email ,  $this->session->email , 
			$this->session->name , 
			$this->input->post("membermailsubject") ,  
			$mailbody 			 );
			
			$this->load->model('Mailbox');
			$data = array(
				'id'  =>   $this->input->post("receipentid") ,  
				'subject' =>   $this->input->post("membermailsubject"),
				'mailbody' =>  $this->input->post("mailbody") ,
				'username' =>  $this->session->name,
				'senderemail' =>  $this->session->email,
				'senderphone' =>   $this->session->phone 
			) ;
			$inbox = $this->Mailbox->send_mail_log($data);   
			redirect('/business/search', 'refresh');  
		}
		$nearest_knows = $this->Knows->search_nearest(  $search_data  ); 
		$pagedata['nearest_knows'] = $nearest_knows;
		 
		//load questions 
		$this->load->model("Questions");
		$pagedata['allquestions'] =$this->Questions->get_questions_bytype('rating'); 
		$pager_config['base_url'] = $this->config->item('base_url') . 'business/search'  ; 
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
		$this->load->view( $view_name ,  $pagedata );
		$this->load->view('common/mail_composer',   $pagedata);
		$this->load->view('template/footer',   $pagedata);
	} 
	
	
	
	public function nearby()
	{
		$uid = $this->session->id;
		if($this->session->role == 'admin')
		{
			redirect('/dashboard', 'refresh'); 
		}
		
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		
		$uid = $this->session->id;
		$offset =  ( intval($this->uri->segment(3)) ?  $this->uri->segment(3) : 0 ) ;  
		$offset2 =  ( intval($this->uri->segment(4)) ?  $this->uri->segment(4) : 0 ) ;  
		$pagedata['offset2'] =$offset2;
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Nearby Members";
		 
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
		$iszip = ( is_int($this->input->post('gscityorzip'))  == true ? 1 : 0 ); 
		$search_data = array( 'uid' =>  $uid);
		$this->load->model("Mc_Business");		
		$nearest_knows = $this->Mc_Business->search_nearby(  $search_data  ); 
		$pagedata['nearest_knows'] = $nearest_knows; 
		$pager_config['base_url'] = $this->config->item('base_url') . 'business/search'; 
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
		$this->load->view('business/nearby',  $pagedata );
		$this->load->view('template/footer',   $pagedata); 
	}
	
}
