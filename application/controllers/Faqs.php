<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faqs extends CI_Controller {

function __construct() {
        parent::__construct();
        
		$this->load->helper( array('form', 'url', 'email', 'cookie' ) );
		$this->load->library(array('session', 'form_validation' ) ); 
		
		
    }  
	public function index()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs();
		$pagedata['title']  = "FAQs";  
		$faq_title = $this->uri->segment('2');
		$this->load->model('MyFaqs'); 
		$pagedata['faq_item'] = null; 		
		if($faq_title != '')
		{ 
			$title_arr =  implode(' ', explode('-', $faq_title) ) ;
			$faq_item = $this->MyFaqs->get_faq_item($title_arr);
			$pagedata['title']  =$faq_item->row()->helptitle;
			$pagedata['faq_item'] = $faq_item; 
		}
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset'); 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/faqs' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	  
	public function help_instruction()
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
		$pagedata['title']  = "Help Instructions"; 
		$this->load->model("Members");
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
		$this->load->model('Groups');  
		$pagedata['groups']  = $this->Groups->getgroups( );  
		
		if($this->input->post("btn_upd_url") == "update_url" )
		{
			$data = array('id' => $uid);
			$pagedata['error'] = $this->Members->update_url($data); 	
		}
		
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs();
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('faqs/help', $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	 
	
	public function mycity_calling_system()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "MyCity Calling System";  
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/mycity_calling_system' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	public function mycity_business_growth()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "MyCity Business Growth";  
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/mycity_business_growth' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	public function voice_mail_drops_and_permission_texting_system()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Voice Mail Drops and Permission Texting System";  
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/voice_mail_drops_and_permission_texting_system' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	public function interview_training_video()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Interview Training Video";   
		$pagedata['faq_menu']  = "faqs/faq_sidebar";  
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/interview_training_video' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	public function people_you_know_your_most_valuable_asset()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Interview Training Video"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/people_you_know_your_most_valuable_asset' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	public function understanding_why_mycity()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Understanding Why Mycity.com"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/understanding_why_mycity' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	public function client_user()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Client / User"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/client_user' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	public function group_referral_partners()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Group Referral Partners"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/group_referral_partners' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	public function search_page()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Search Page"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/search_page' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	public function contact_us()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Contact Us"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/contact_us' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	public function adding_mycity_to_your_group()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Looking to add mycity to your group or want to start your own group?"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/adding_mycity_to_your_group' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	
	public function setting_up_account()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Video - Setting Up Your Account"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/setting_up_account' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	public function sales_tips()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Sales Tips"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/sales_tips' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	
	public function sales_tip_triggers()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Sales Tip - Triggers"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/sales_tip_triggers' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	
	public function suggestion_referral_tool()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Suggestion Referral Tool"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/suggestion_referral_tool' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	
	public function introduction_referrals_help()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Introduction/Referral Help"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/introduction_referrals_help' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	
	public function rating_system()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "OUR RATING SYSTEM helps your highly rated people you know receive referrals!"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/rating_system' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	
	
	public function uploading_linkedin_connections()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Uploading your LinkedIn connections"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/uploading_linkedin_connections' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	public function search_box()
	{
		$this->load->model('MyFaqs');  
		$pagedata['faqs'] = $this->MyFaqs->get_faqs(); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "The Search Box"; 
		$pagedata['faq_menu']  = "faqs/faq_sidebar"; 		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('faqs/search_box' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	public function edit()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id;  
		if($this->session->role != 'admin'  )
		{
			redirect('/dashboard', 'refresh'); 
		} 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Edit About Text";  
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
		$this->load->model("MyFaqs"); 
		
		$edit_id = intval( $this->uri->segment('3')) > 0 ? $this->uri->segment('3') : 0 ;
		$faq_item = null;
		if($edit_id > 0)
		{
			$faq_item =$this->MyFaqs->get_faq_by_id( $edit_id );
			 
		}
		
		$pagedata['faq_item']= $faq_item; 
		if($this->input->post('save_faq') == 'save')
		{
			$faqid= $title = $this->input->post("faqid");
			$help_url= $title = $this->input->post("title");
			$video_url = $this->input->post("video_url");
			$help_text = $this->input->post("help_text"); 
			
			if($faqid ==0)
			{
				$help_url = str_replace(',', ' ', $help_url); 	
				$help_url = str_replace('/', ' ', $help_url); 
				$help_url = str_replace('\'', ' ', $help_url); 
				$help_url = str_replace('"', ' ', $help_url); 
				$help_url_arr = array_filter( explode(' ' , $help_url ) ); 
				$help_url = implode('-', $help_url_arr);
	   
				$data_faq = array (
					'helpurl' => $help_url,
					'helptitle' => $title,
					'helpvideo' => $video_url,
					'helptext' => $help_text,
					'publish' => 1,
					'position' => '999'		
				);
				
				$helpid =$this->MyFaqs->add( $data_faq );
				if($helpid > 0)
				{
					$this->session->set_userdata("msg_error", "Faq entry saved!");
				}
				else 
				{
					$this->session->set_userdata("msg_error", "Faq could not be saved!");
				}
			}
			else 
			{ 
				$data_faq = array ( 
					'helptitle' => $title,
					'helpvideo' => $video_url,
					'helptext' => $help_text,
					'publish' => 1 	
				); 
				$update_cnt =$this->MyFaqs->update( $data_faq, $faqid );
				if($update_cnt > 0)
				{
					$this->session->set_userdata("msg_error", "Faq entry updated!");
				}
				else 
				{
					$this->session->set_userdata("msg_error", "Faq could not be updated!");
				}
				 
			}
			redirect(current_url() , 'refresh'); 
		}
		
		$allfaqs = $this->MyFaqs->get_faqs();
		$pagedata['allfaqs'] = $allfaqs;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata);
		$this->load->view('template/navigation_side',   $pagedata); 		
		$this->load->view('faqs/manage', $pagedata);  
		$this->load->view('template/footer',   $pagedata); 
	} 
}
