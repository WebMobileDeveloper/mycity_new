<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Dashboard extends CI_Controller 
{
	function __construct() {
        parent::__construct();
        $this->load->helper( array('form', 'cookie',  'url')); 
		$this->load->library(  array( 'session', 'pagination' ) );
		$this->load->model('Members');    
    }  
	public function index()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		
		$uid = $this->session->id;
		if(  $this->session->role == 'admin'  )
		{
			//delete_cookie('_mcu');
		    //$this->session->sess_destroy();
//			redirect('/admin/dashboard.php', 'refresh');
            header("Location: /admin/dashboard.php");
		}
		
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Dashboard"; 
		
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
		$this->load->model('MyCountry');
		$allcountry = $this->MyCountry->get_all();
		$pagedata['allcountry'] = $allcountry; 
		
		$this->load->model('Vocations');  
		$pagedata['vocations']  = $this->Vocations->get_vocations( );  
		$this->load->model('Groups');  
		$pagedata['groups']  = $this->Groups->getgroups( );  
		
		if($this->input->post("btn_upd_url") == "update_url" )
		{
			$data = array('id' => $uid); 
			$pagedata['error'] = $this->Members->update_url($data); 
			//redirect('/dashboard','refresh'); 
		} 
		if($this->input->post("btn_join_prg") == "join_program")
		{
			$ppid = $this->input->post("ppid");
			$this->load->model('Programparticipant'); 
			$this->Programparticipant->join_program( array( 'id'=> $uid , 's' => '1' ,'ppid'=>'0' )   ); 
		}
		
		if($this->input->post("btn_save") == "password" )
		{
			$old_pass = $this->input->post("old_pass");
			$new_pass = $this->input->post("new_pass"); 
			$err = $this->Members->update_password($uid, md5($old_pass), md5($new_pass));
			$error = array('error' =>  $err ); 
			$pagedata['error'] =$error;
		}
		
		if($this->input->post("upload_btn") == "upload" )
		{
			$filename =  "prof-img-". $uid. "-" . date("Ymd-His") ; 
			$config['upload_path']    =  $this->config->item('profile_img');  
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size']   = 0;
			$config['max_width'] = 0;
			$config['max_height'] = 0;
			$config['file_name'] =  $filename ; 
			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('prof_img'))
			{
				$error = array('error' => $this->upload->display_errors()); 
				$pagedata['error'] =$error;
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());
				$this->load->model("Members"); 
				$extension = pathinfo( $this->upload->data('file_name') , PATHINFO_EXTENSION);
				$this->Members->update( array('image' => $filename . "." . $extension  ) , $uid );  
			}
			//redirect('/dashboard','refresh');
		} 
		
		if($this->input->post("btn_update") == "update_profile")
		{
			$upd_username = $this->input->post("upd_username");
			$upd_phone =$this->input->post("upd_phone");
			$upd_country = $this->input->post("upd_country");
			$upd_street = $this->input->post("upd_street");
			$upd_city = $this->input->post("upd_city"); 
			$upd_zip = $this->input->post("upd_zip");
			$upd_email = $this->input->post("upd_email");
			$upd_public_private = $this->input->post("upd_public_private");
			$upd_reminder_email =$this->input->post("upd_reminder_email");
			$about_your_self = $this->input->post("about_your_self");
			$linkedin_profile = $this->input->post("linkedin_profile");
			if($this->session->role == 'admin')
					$member_tags = $this->input->post("member_tags");
			else 
				$member_tags = '';
			
			$mygroups =   '' ;
			
			if(sizeof( $this->input->post('upd_usergrp')  ) > 0 )
				$mygroups  = implode(',', $this->input->post('upd_usergrp') ) ; 
			
			$myvocations = '' ; 
			if(sizeof( $this->input->post('upd_uservoc')  ) > 0 )
				$myvocations  = implode(',', $this->input->post('upd_uservoc') ) ;
			
			$mytargets =  '' ;
			if(sizeof( $this->input->post('upd_usertarget')  ) > 0 )
				$mytargets  = implode(',', $this->input->post('upd_usertarget') ) ;
			
		 
			$mytargetreferrals =  '' ;
			if(sizeof( $this->input->post('upd_usertargetreferral')  ) > 0 )
				$mytargetreferrals  = implode(',', $this->input->post('upd_usertargetreferral') ) ;
			 
			$is_business = $this->input->post('membertype_edit');
			
			$data1 = array(
			 
			'user_phone'=> $upd_phone,  
			'profileisvisible'=> $upd_public_private, 
			'tags' => $member_tags, 
			'user_type'=>$is_business 
			) ; 
		  
			if($is_business ==  1)
			{
				$data1['busi_name'] = $this->input->post('busi_name_edit');
				$data1['busi_location'] = $this->input->post('busi_location_edit');
				$data1['busi_type'] = $this->input->post('busi_type_edit');
				$data1['busi_hours'] = $this->input->post('busi_hours_edit');
				$data1['busi_website'] =$this->input->post('busi_website_edit');
				$data1['busi_location_street'] = $this->input->post('busi_location_street_edit');
			} 
			$profile_data = array(
			'country'=> $upd_country,
			'street'=> $upd_street,
			'city'=> $upd_city,
			'zip'=> $upd_zip, 
			'about_your_self'=> $about_your_self, 
			'groups' =>$mygroups,
			'vocations'=>$myvocations,
			'target_clients'=>$mytargets,
			'target_referral_partners'=>$mytargetreferrals ,
			'linkedin_profile' => $linkedin_profile
			) ; 
		 $this->load->model('Userdetails');
		 $this->Userdetails->update($profile_data, $uid );
		 $this->Members->update_profile($data1, $uid );
		}
		$this->load->model('MyStatement');
		if($this->input->post('save_note') == 'save')
		{
			$notearray = array( 'enteredon' =>  date('Y-m-d H:i:s') , 'user_id' =>  $uid, 'note' => $this->input->post('instantnote') );
			$noteid = $this->MyStatement->add( $notearray );
			$error['error'] = ($noteid == 0 ? "Note could not be added." : "Note saved successfully!" ); 
		} 
		$statements = $this->MyStatement->get_statements(  );
		$pagedata['statements'] = $statements; 
 
		$pagedata['member']  = $this->Members->getprofile( $uid );
		$this->load->model('MyExcelImportLog'); 
		$all_excels = $this->MyExcelImportLog->get_file_details($uid); 
		$pagedata['all_excels'] = $all_excels ;
		 
		$this->load->model('MyProfileViewLog'); 
		$viewlog = $this->MyProfileViewLog->get_logs_by_id( array('id' => $uid , 'offset' => 0));
		$pagedata['profileviewlog']  = $viewlog;
		 
		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('dashboard', $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	public function referrals()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id;
		$offset=  $this->uri->segment(3);  
		 
		 
		if( !intval($offset)  )
		{
			$offset =0; 
		}
		 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Introduce Referrals"; 
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
					'helpvideo' =>  $row-> helpvideo ) 
				); 
		}
		$pagedata['help_data_buttons']  = $button_array;
		$this->load->model('Knows'); 
		
		if($this->input->post("remknows") == '1')
		{
			$this->load->model("MyReferralSuggestions");
			$this->MyReferralSuggestions->remove_batch( $this->input->post("kids")  );
		}  
		//$referrals =$this->Knows->do_referral_mapping( $uid , 'Mi');  
		$activepage=1;
		$ssf=0;
		if($this->input->post('activepage'))
		{
			$activepage = $this->input->post('activepage');
		}
		
		if($this->input->post('ssf'))
		{
			$ssf = $this->input->post('ssf');
		}
		
		$my_referrals =$this->Knows->show_introduction( 
			array('uid' => $uid , 'pagesize' => 10, 'offset' => $offset , 'ssf'=> $ssf )   );
		//load mail templates
		$this->load->model('MyMailTemplates'); 
		$pagedata['mailtemplates'] = $this->MyMailTemplates->get_template_by_name('Introduction/Referral');
		$pagedata['pager'] = array(  'pagesize' => 10, 'activepage' =>$activepage, 'ssf'=> $ssf )  ;
		$pagedata['referrals'] = $my_referrals; 
		$pagedata['offset'] = $offset; 
		$pager_config['base_url'] = $this->config->item('base_url') . 'dashboard/referrals/' ;	 
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
		$this->load->view('know/my_referrals',   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	public function performance()
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
		$pagedata['title']  = "MyCity"; 
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
				'helpvideo' =>  $row-> helpvideo )  ); 
		}
		$pagedata['help_data_buttons']  = $button_array;
		$performancedata = $this->Members->performance($uid);
		
		$pagedata['performancedata'] = $performancedata;
		 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('member/performance'); 
		$this->load->view('template/footer',   $pagedata); 
	}  
	 
	public function reverse_tracking()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id;
		
		if($this->session->role != 'admin')
		{
			redirect('/dashboard', 'refresh'); 
		}
		 
		$offset =  ( intval($this->uri->segment(3)) ?  $this->uri->segment(3) : 0 ) ; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Reverse Tracking"; 
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
					'helpvideo' =>  $row-> helpvideo )
			); 
		}
		$pagedata['help_data_buttons']  = $button_array; 
		$this->load->model('Vocations');  
		$pagedata['vocations']  = $this->Vocations->get_vocations( );  
		$this->load->model('Groups');  
		$pagedata['groups']  = $this->Groups->getgroups( );  
		$this->load->model('Tags');  
		$pagedata['tags']  = $this->Tags->gettags( );  
		$this->load->model('Lifestyles');  
		$pagedata['lifestyles']  = $this->Lifestyles->getlifestyles( );
		$this->load->model('Knows');  
		$result =false;
		if($this->input->post("btn_search") == "reverse_track")
		{
			$filter_ls=  $this->input->post("lifestyles") ;
			$filter_tag=  $this->input->post("tags") ;
			$filter_zip=  $this->input->post("tbzip") ;
			$filter_voc=  $this->input->post("vocations") ;
			$filter_city=  $this->input->post("city") ;
			$filter_keyword=  $this->input->post("keyword") ;
		
			$search_filter = array('key' => $this->input->post("keyword"),
			'lifestyle'=> $this->input->post("lifestyles"),
			'tags' =>  $this->input->post("tags") ,
			'zip' =>  $this->input->post("tbzip"), 
			'vocations' =>  $this->input->post("vocations"),
			'location' =>  $this->input->post("city"),
			'keyword' =>  $this->input->post("keyword"), 
			'offset' => $offset, 'size' => '10' );
			
			$this->session->set_userdata('revtrack_filter', $search_filter ); 
			$result = $this->Knows->reverse_tracking($search_filter); 
		} 
		else if(  $this->session->has_userdata('revtrack_filter' ))
		{
			$offset = 0;
			if($this->uri->segment(3) > 0)
			{
				$offset = $this->uri->segment(3);
				 
				
				$search_filter = array('key' => $this->session->revtrack_filter["keyword"],
				'lifestyle'=> $this->session->revtrack_filter["lifestyle"],
				'tags' =>  $this->session->revtrack_filter["tags"] ,
				'zip' =>  $this->session->revtrack_filter["zip"], 
				'vocations' =>  $this->session->revtrack_filter["vocations"],
				'location' =>  $this->session->revtrack_filter["location"],
				'keyword' =>  $this->session->revtrack_filter["keyword"], 
				'offset' => $offset, 
				'size' => '10' );
				$this->session->set_userdata('revtrack_filter', $search_filter ); 
				$result = $this->Knows->reverse_tracking($search_filter);  
			} 
		} 
		
		
		$pager_config['base_url'] = $this->config->item('base_url') . 'dashboard/reverse-tracking'  ; 
		$pager_config['per_page'] =10; 
		$pager_config['full_tag_open'] = "<ul class='pagination'>";
		$pager_config['full_tag_close'] ="</ul>";
		$pager_config['num_tag_open'] = '<li>'; 
		$pager_config['num_tag_close'] = '</li>';
		$pager_config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		$pager_config['cur_tag_close'] = "<span class='sr-only'></span></a></li>"; 
		$pager_config['next_link'] = 'Next →';
		$pager_config['next_tag_open'] = "<li>";
		$pager_config['next_tagl_close'] = "</li>"; 
		$pager_config['prev_link'] = '← Previous';
		$pager_config['prev_tag_open'] = "<li>";
		$pager_config['prev_tagl_close'] = "</li>"; 
		$pager_config['first_link'] = '« First'; 
		$pager_config['first_tag_open'] = "<li>";
		$pager_config['first_tagl_close'] = "</li>"; 
		$pager_config['last_link'] = 'Last »';
		$pager_config['last_tag_open'] = "<li>";
		$pager_config['last_tagl_close'] = "</li>"; 				
		$pagedata['pager_config'] = $pager_config ;
		
		$pagedata['reverse_maps'] = $result; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('tools/reverse_tracking',   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	public function client_tracking()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id;
		
		if(  $this->session->role != 'admin' )
		{
			redirect('/dashboard', 'refresh'); 
		}
		$role = $this->session->role;
		 
		$utype = $this->uri->segment(3);
		switch($utype)
		{
			case 'active':
				$status =1;
				break;
			case 'inactive':
				$status =0;
				break;
			case 'ex':
				$status =10;
				break;
			default: 
				$status =1;
				$utype ='active'; 
				$array_items = array('p1c' => '0', 'p2c' => '0', 'p3c' => '0' , 'p1o' => '0', 'p2o' => '0', 'p3o' => '0'  );
				$this->session->set_userdata($array_items ); 
				break; 
		}  
		$pagedata['status'] = $status; 
		 
		if($this->uri->segment(4) == '')
		{
			$offset=0;
		}
		else 
		{
			$offset = $this->uri->segment(4); 
			switch($utype)
			{
				case 'active':
					$pager_log['p1c']= $offset;
					$pager_log['p1o']= ($this->session->p1c? $this->session->p1c : $offset);
					break;
				case 'inactive':
					$pager_log['p2c']= $offset;
					$pager_log['p2o']= ($this->session->p2c? $this->session->p2c : $offset);
							
					break;
				case 'ex':
					$pager_log['p3c']= $offset;
					$pager_log['p3o']= ($this->session->p3c? $this->session->p3c : $offset);
					break;  
			}  
		}

		switch($utype)
			{
				case 'active':
					$pager_log['p1c']= $offset;
					$pager_log['p1o']= ($this->session->p1c? $this->session->p1c : $offset);
					break;
				case 'inactive':
					$pager_log['p2c']= $offset;
					$pager_log['p2o']= ($this->session->p2c? $this->session->p2c : $offset);
							
					break;
				case 'ex':
					$pager_log['p3c']= $offset;
					$pager_log['p3o']= ($this->session->p3c? $this->session->p3c : $offset);
					break;  
			} 

		$this->session->set_userdata( $pager_log ); 
		//echo "<br/>offset old: " . $this->session->p1o . " new:" . $this->session->p1c;
		//echo "<br/>offset old: " . $this->session->p2o . " new:" . $this->session->p2c ;	
		//echo "<br/>offset old: " . $this->session->p3o . " new:" . $this->session->p3c ;	
		
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['profile_img'] = $this->config->item('profile_img');
		$pagedata['site_path'] = $this->config->item('site_path');
		$pagedata['title']  = "Client Tracking";  
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
				'helpvideo' =>  $row-> helpvideo )  ); 
		}
		$pagedata['help_data_buttons']  = $button_array;
		$performancedata = $this->Members->performance($uid);
		
		$pagedata['performancedata'] = $performancedata;
		$this->load->model('MyClientTracking');    
		if($this->input->post('move_user') == '0' || 
		$this->input->post('move_user') == '1' || 
		$this->input->post('move_user') == '10'  )
		{
			$newstatus = $this->input->post('move_user');
			$userids = $this->input->post('cb_actmembers');
			$this->MyClientTracking->member_statusupdate( implode(',', $userids) , $newstatus );
			//redirect('/dashboard/client-tracking/' . $utype , 'refresh');
		}
		
		$members = $this->MyClientTracking->get_all_members( "", $status, $offset, 10 ); 
		$pagedata['members'] = $members ; 
		if($this->uri->segment(5) != '' && $this->uri->segment(5) > 0)
		{
			$this->load->model("MyEmailProgramAssigned");
			$timeline = $this->MyEmailProgramAssigned->get_timeline($this->uri->segment(5));
			$pagedata['timeline'] = $timeline; 
			$pagedata['timeline_user']  = $this->Members->getprofile($this->uri->segment(5));
			
		}
		else 
		{
			$pagedata['timeline'] = null;
		}
		
		$pager_config['base_url'] = $this->config->item('base_url') . 'dashboard/client-tracking/' . $utype . "/" ; 
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
		$this->load->view('dashboard/client_tracking'); 
		$this->load->view('template/footer',   $pagedata); 
	}  
	
	
	public function setup_email()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id;
		if(  $this->session->role != 'admin' )
		{
			redirect('/dashboard', 'refresh'); 
		}
		$role = $this->session->role;
		
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Configure Email Template"; 
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
				'helpvideo' =>  $row-> helpvideo )  ); 
		} 
		$pagedata['help_data_buttons']  = $button_array;
		
	 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('dashboard/email_setup', $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	public function clients_voice_mails()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		
		$uid = $this->session->id;
		if(  $this->session->role != 'admin' )
		{
			redirect('/dashboard', 'refresh'); 
		}
		$role = $this->session->role;
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['profile_img'] = $this->config->item('profile_img');
		$pagedata['site_path'] = $this->config->item('site_path'); 
		$pagedata['title']  = "Configure Email Template"; 
		$this->load->model('Members'); 
		$pagedata['member']  = $this->Members->getprofile( $uid );
		
		$pagedata['staffs']  = $this->Members->get_all_staffs(  );
		
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
		$this->load->model('MyClientTracking');
		
		if($this->input->post('btn_remove') == 'rem' )
		{
			$taskid  = $this->input->post('taskid');
			$this->MyClientTracking->remove($taskid);
		}
		if($this->input->post('add_voicemail') == 'save' )
		{
			$adate =  preg_split("/(\/|-)/",  $this->input->post('vm_assigndate')  ); 
			
			if(sizeof($adate) == 3 )
			{
				$schedule_date  = $adate[2] . "/" .  $adate[1]  . "/". $adate[0] . " " . 
				$this->input->post('vm_schedulehr') . ":" . $this->input->post('vm_schedulemin') . " " . 
				$this->input->post('vm_scheduleper'); 
				$stime = strtotime( $schedule_date );
				$schedule_date = date('Y-m-d H:i:s',$stime);
			}				
			else 
			{
				$schedule_date = date("Y/m/d H:i:s");
			}
			
			$newvoicemail = array(	
			'a_date' => $schedule_date ,
			'client_id' => $this->input->post('clientid'), 
			'description' => $this->input->post('vm_description') );
			
			$vmid = $this->input->post('vmid');
			if($vmid == 0 )
			{
				$this->MyClientTracking->add($newvoicemail  ); 
			}
			else 
			{
				$this->MyClientTracking->update($newvoicemail, $vmid ); 
			}
			
			redirect(current_url(), 'refresh');  
		}
		  
		if($this->uri->segment(3) == '')
		{
			if($this->session->pg_cvo > 0)
				$offset=$this->session->pg_cvo; 
			else 
				$offset=0;
			 
			$array_items = array('pg_cvc' =>  $offset , 'pg_cvo' =>  $offset );
		}
		else 
		{
			$offset =$this->uri->segment(3); 
			$array_items = array('pg_cvc' =>  $offset , 'pg_cvo' =>  $this->session->pg_cvc );
		}
		
		$this->session->set_userdata($array_items ); 
		$data =  array('offset' => $offset, 'limit'=> '10', 'keyword' => '', 'condition' => 'in'); 
		$voicemails = $this->MyClientTracking->get_voicemails( $data ); 
		$pagedata['voicemails'] = $voicemails ;
		
		if($this->uri->segment(4) > 0)
		{
			//get voicemail timeliine
			$pagedata['voicemail_user']  = $this->Members->getprofile( $this->uri->segment(4) );
			$voice_timeline = $this->MyClientTracking->get_voicemail_timeline( $this->uri->segment(4) );  
		}
		else
		{
			$voice_timeline = null;
			$pagedata['voicemail_user']  =null;
		}
		$pagedata['voice_timeline'] = $voice_timeline;  
		$pager_config['base_url'] = $this->config->item('base_url') . 'dashboard/clients-voice-mails/'   ; 
		$pager_config['per_page'] = 10 ; 
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
		$pager_config['total_rows'] = $voicemails['num_rows'];		
		$pagedata['pager_config'] = $pager_config ; 
		$this->pagination->initialize($pager_config);  
		$pagedata['tab1'] = 'active';
		$pagedata['tab2'] = ''; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('dashboard/voicemails_log', $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	
	
	public function clients_no_voice_mails()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		
		$uid = $this->session->id;
		if(  $this->session->role != 'admin' )
		{
			redirect('/dashboard', 'refresh'); 
		}
		$role = $this->session->role;
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['profile_img'] = $this->config->item('profile_img');
		$pagedata['site_path'] = $this->config->item('site_path'); 
		$pagedata['title']  = "Configure Email Template"; 
		$this->load->model('Members'); 
		$pagedata['member']  = $this->Members->getprofile( $uid );
		$pagedata['staffs']  = $this->Members->get_all_staffs(  );
		
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
		
		$this->load->model('MyClientTracking');
		
		$this->load->model('MyClientTracking');
		
		if($this->input->post('btn_remove') == 'rem' )
		{
			$taskid  = $this->input->post('taskid');
			$this->MyClientTracking->remove($taskid);
		}
		if($this->input->post('add_voicemail') == 'save' )
		{
			$adate =  preg_split("/(\/|-)/",  $this->input->post('vm_assigndate')  ); 
			
			if(sizeof($adate) == 3 )
			{
				$schedule_date  = $adate[2] . "/" .  $adate[1]  . "/". $adate[0] . " " . 
				$this->input->post('vm_schedulehr') . ":" . $this->input->post('vm_schedulemin') . " " . 
				$this->input->post('vm_scheduleper'); 
				$stime = strtotime( $schedule_date );
				$schedule_date = date('Y-m-d H:i:s',$stime);
			}				
			else 
			{
				$schedule_date = date("Y/m/d H:i:s");
			}
			
			$newvoicemail = array(	
			'a_date' => $schedule_date ,
			'client_id' => $this->input->post('clientid'), 
			'description' => $this->input->post('vm_description') );
			
			$vmid = $this->input->post('vmid');
			if($vmid == 0 )
			{
				$this->MyClientTracking->add($newvoicemail  ); 
			}
			else 
			{
				$this->MyClientTracking->update($newvoicemail, $vmid ); 
			}
			
			redirect(current_url(), 'refresh');  
		}
		 
		
		if($this->uri->segment(3) == '')
		{
			if($this->session->pg_cnvo > 0)
				$offset=$this->session->pg_cnvo;  
			else 
				$offset=0;   
			$array_items = array('pg_cnvc' =>  $offset , 'pg_cnvo' =>  $offset );
		}
		else 
		{
			$offset =$this->uri->segment(3); 
			$array_items = array('pg_cnvc' =>  $offset , 'pg_cnvo' =>  $this->session->pg_cnvc );
		} 
		
		
		$this->session->set_userdata($array_items ); 
		$data =  array('offset' => $offset, 'limit'=> '10', 'keyword' => '', 'condition' => 'in'); 
		$voicemails = $this->MyClientTracking->get_voicemails( $data ); 
		$pagedata['voicemails'] = $voicemails ;
		
		if($this->uri->segment(4) > 0)
		{
			//get voicemail timeliine
			$pagedata['voicemail_user']  = $this->Members->getprofile( $this->uri->segment(4) );
			$voice_timeline = $this->MyClientTracking->get_voicemail_timeline( $this->uri->segment(4) );  
	 
		
		}
		else
		{
			$voice_timeline = null;
			$pagedata['voicemail_user']  =null;
		} 
		
		$pagedata['voice_timeline'] = $voice_timeline;  
		$this->session->set_userdata($array_items );  
		$data =  array('offset' => $offset, 'limit'=> '10', 'keyword' => '', 'condition' => 'not in'); 
		$voicemails = $this->MyClientTracking->get_voicemails( $data ); 
		$pagedata['voicemails'] = $voicemails ;
		$this->session->set_userdata($array_items ); 
		
		$pager_config['base_url'] = $this->config->item('base_url') . 'dashboard/client-without-voice-mails/'   ; 
		$pager_config['per_page'] = 10 ; 
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
		$pager_config['total_rows'] = $voicemails['num_rows'];		
		$pagedata['pager_config'] = $pager_config ; 
		$this->pagination->initialize($pager_config);
		$pagedata['tab1'] = '';
		$pagedata['tab2'] = 'active';
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('dashboard/voicemails_log', $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	 
	function switchaccount()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		
		if($this->session->admin_switch != 'on')
		{
			redirect('/dashboard', 'refresh');  
		}
		$this->load->model('Members');
		$member_to_switch = $this->Members->getprofile( 1 ); 
		$profile = $member_to_switch->row_array( );  
		if($profile['id'] != 0)
		{
			//clear previous cookie and session
			delete_cookie('_mcu'); 
			$this->session->sess_destroy();
		
			//refresh cookie and session	
			$this->session->set_userdata( $profile ); 
			$cookie = array(
			'name'   => '_mcu', 
			'value'  => json_encode( $profile ) ,
			'expire' => time()+86500, 
			'path'   => '/' 
			); 
				$this->input->set_cookie(   $cookie  );
				$log_err= '';
				redirect('/dashboard', 'refresh'); 
		}
		else 
		{
			$log_err = 'Email or password is not correct.';
		} 
	}
	
	public function top_rated_knows()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id;
		
//		if(  $this->session->role != 'admin' )
//		{
//			redirect('/dashboard', 'refresh');
//		}
		$role = $this->session->role;
		
		$action =  $this->uri->segment(3);    
		if( !intval($action)  )
		{
			$offset =  $this->uri->segment(4);  
			if( !intval($offset)  )
			{
				$offset = 0; 
			}	

			$knowid =  $this->uri->segment(5);  
			if( !intval($knowid)  )
			{
				$knowid = 0; 
			}
		}
		else 
		{
			$knowid =  $this->uri->segment(5);  
			if( !intval($knowid)  )
			{
				$knowid = 0; 
			}  
			$offset=  $action;  
			$action = '';
		} 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Introduce Referrals"; 
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
					'helpvideo' =>  $row-> helpvideo ) 
				); 
		}
		$pagedata['help_data_buttons']  = $button_array;
		$this->load->model('Knows');  
		//$referrals =$this->Knows->do_referral_mapping( $uid , 'Mi');  
		$activepage=1;
		$ssf=0;
		if($this->input->post('activepage'))
		{
			$activepage = $this->input->post('activepage');
		} 
		$pagedata['mailbody'] ='';
		if($action =='invite')
		{
			$pagedata['editor'] = true; 
			$pagedata['cur_url'] = 'inviteknow';
			if($knowid > 0)
			{
				$know_details =$this->Knows->get_know_profile($knowid);
				$pagedata['know_info'] = $know_details; 
			} 
			//loading mail template 
			$ds = DIRECTORY_SEPARATOR;  
			$path =  $_SERVER['DOCUMENT_ROOT'] . $ds    ;   
			if(  file_exists( $path . "templates/claim_your_profile.txt" ) )
			{
				$mailbody = file_get_contents( $path . "templates/claim_your_profile.txt" ) ;   
			}
			else
			{
				$mailbody = "Profile claim email template missing. Please compose it here."  ;
			}
			$pagedata['mailbody'] = $mailbody;	
 		
		}
		else 
		{
			$pagedata['cur_url'] = '';
			$pagedata['editor'] = false;  
			$pagedata['know_info'] =  null;  
		}
		 
		if($this->input->post("btn_send_email") == "send")
		{
			$email = $this->input->post("knowinviteemail");
			$reciepentid = $this->input->post("knowid"); 
			
			$rst =  $this->db->query("select count(*) as ecount from mc_claimprofile_invite where user_id='$reciepentid'"); 
			if($rst->num_rows() > 0)
			{
				if($rst->row()->ecount == 0)
				{
					$this->db->query("insert into mc_claimprofile_invite 
					(user_id,   invitedate ) VALUES ('$reciepentid' ,  NOW() )");
				}
			}
			//update profile claim  
			$sql_query =  "update user_people  set isinvited='1' where id= ?" ;
			$this->db->query( $sql_query, array($reciepentid) ); 
		}
		
		
		$rated_knows =$this->Knows->get_top_rated_knows(  array('ranking' => '25' ,  'offset' => $offset )   );  
		$pagedata['rated_knows'] = $rated_knows; 
		$pagedata['offset'] = $offset;
		$pager_config['base_url'] = $this->config->item('base_url') . 'dashboard/top-rated-knows/' ;
        $pager_config['total_rows'] = $rated_knows['num_rows'];
		$pager_config['per_page'] = 10;
		$pager_config['uri_segment'] = 3;
        // custom paging configuration
        $pager_config['num_links'] = 5;
        $pager_config['use_page_numbers'] = TRUE;
        $pager_config['reuse_query_string'] = TRUE;

//		$pager_config['full_tag_open'] = "<ul class='pagination'>";
//		$pager_config['full_tag_close'] ="</ul>";
//
//		$pager_config['num_tag_open'] = '<li>';
//		$pager_config['num_tag_close'] = '</li>';
//
//		$pager_config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
//		$pager_config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
//
//		$pager_config['next_tag_open'] = "<li>";
//		$pager_config['next_tagl_close'] = "</li>";
//
//		$pager_config['prev_tag_open'] = "<li>";
//		$pager_config['prev_tagl_close'] = "</li>";
//
//		$pager_config['first_tag_open'] = "<li>";
//		$pager_config['first_tagl_close'] = "</li>";
//
//		$pager_config['last_tag_open'] = "<li>";
//		$pager_config['last_tagl_close'] = "</li>";


        $pager_config['full_tag_open'] = "<ul class='pagination'>";
        $pager_config['full_tag_close'] = "</ul>";
        $pager_config['cur_page'] = $offset+1;
        $pager_config['anchor_class'] = "page";

        $pager_config['first_link'] = '&Lt;';
        $pager_config['first_tag_open'] = '<li>';
        $pager_config['first_tag_close'] = '</li>';

        $pager_config['last_link'] = '&Gt;';
        $pager_config['last_tag_open'] = '<li class="lastlink">';
        $pager_config['last_tag_close'] = '</li>';

        $pager_config['next_link'] = '&gt;';
        $pager_config['next_tag_open'] = '<li class="nextlink">';
        $pager_config['next_tag_close'] = '</li>';

        $pager_config['prev_link'] = '&lt;';
        $pager_config['prev_tag_open'] = '<li class="prevlink">';
        $pager_config['prev_tag_close'] = '</li>';

		$pager_config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		$pager_config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";

        $pager_config['num_tag_open'] = '<li class="numlink">';
        $pager_config['num_tag_close'] = '</li>';

        $this->pagination->initialize($pager_config);
        $pagedata["links"] = $this->pagination->create_custom_links();

		$pagedata['pager_config'] = $pager_config ; 
		$pagedata['knowid'] = $knowid;
		$this->load->view('template/head',   $pagedata);
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('dashboard/top_rated_knows',   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	 
	public function search_log()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id;
		
		if(  $this->session->role != 'admin' )
		{
			redirect('/dashboard', 'refresh'); 
		}
		$role = $this->session->role;
		
		$offset =  $this->uri->segment(3); 
		if( !intval($offset)  )
		{
			$offset = 0; 
		}	
  
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Search Log"; 
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
					'helpvideo' =>  $row-> helpvideo ) 
				); 
		}
		$pagedata['help_data_buttons']  = $button_array;
		
		$this->load->model("MyGlobalSearchLog");
		$logs = $this->MyGlobalSearchLog->get_all_logs(array('offset' => $offset));
		$pagedata['logs'] = $logs;  
		$pagedata['offset'] = $offset; 
		$pager_config['base_url'] = $this->config->item('base_url') . 'dashboard/search-log/' ;	 
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
		$this->load->view('dashboard/search_log',   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	public function profilevisitlogs()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id;
		
		if(  $this->session->role != 'admin' )
		{
			redirect('/dashboard', 'refresh'); 
		}
		$role = $this->session->role;
		
		$offset =  $this->uri->segment(3); 
		if( !intval($offset)  )
		{
			$offset = 0; 
		}	
		
		$userlogid =  $this->uri->segment(4); 
		if( !intval($userlogid)  )
		{
			$userlogid = 0; 
		}
		$pagedata['userlogid']  =$userlogid  ;
  
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Profile Visits History"; 
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
					'helpvideo' =>  $row-> helpvideo ) 
				); 
		}
		$pagedata['help_data_buttons']  = $button_array;
		
		$this->load->model("MyProfileViewLog");
		$logs = $this->MyProfileViewLog->get_logs(array('offset' => $offset));
		
		$pagedata['logs'] = $logs;  
		$pagedata['offset'] = $offset; 
		$pager_config['base_url'] = $this->config->item('base_url') . 'dashboard/search-log/' ;	 
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
		$this->load->view('dashboard/profile_view_log',   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
}
