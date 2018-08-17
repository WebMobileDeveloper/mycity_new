<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller 
{
	function __construct() {
        parent::__construct();
		$this->load->library(array('session', 'pagination' ) );
		$this->load->helper( array('form', 'url', 'email' , 'array', 'cookie') );  
		
	 
    } 
	
	public function index()
	{ 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		
		if($this->session->role == 'user')
		{
			redirect('/dashboard', 'refresh'); 
		} 
		
		$offset =  ( intval($this->uri->segment(2)) ?  $this->uri->segment(2) : 0 ) ;  
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
		$pagedata['tags']  = $this->Tags->gettags();
		$this->load->model('MyCountry');  
		$pagedata['country']  = $this->MyCountry->get_all(); 
		$this->load->model('Members'); 
		
		if( $this->input->post('btn_delmember') == 'rem' )
		{
			 $this->Members->remove( $this->input->post('userid') ); 
		}
		 
		
		if( $this->input->post('btn_search') == 'search' )
		{
			$search_filter  =array(
			'username' => $this->input->post('srchRefName'),
			'createdOn'=> $this->input->post('srchentryDate'),
			'user_email' => $this->input->post('srchemail'),
			'user_phone' => $this->input->post('srchPhone'),
			'city' => $this->input->post('filtercity'),
			'zip' => $this->input->post('srchZipCode'),
			'tags' => $this->input->post('filterTags'), 
			'vocations' => $this->input->post('locateVoc'),
			'offset' => $offset , 
			'limit'	=> 10 
			);  
			
			$member_search_filter  = array(
			'sess_user_name' => $this->input->post('srchRefName'),
			'sess_entry_date'=> $this->input->post('srchentryDate'),
			'sess_user_email' => $this->input->post('srchemail'),
			'sess_user_phone' => $this->input->post('srchPhone'),
			'sess_user_city' => $this->input->post('filtercity'),
			'sess_user_zip' => $this->input->post('srchZipCode'),
			'sess_user_tag' => $this->input->post('filterTags'), 
			'sess_user_voc' => $this->input->post('locateVoc'),
			'sess_offset' => $offset , 
			'sess_limit'	=> 10 ); 
			
			  
			$allmembers = $this->Members->get_all_members($search_filter);   
			
			$pagedata['allmembers'] = $allmembers ;
			$this->session->set_userdata($member_search_filter);  
		}
		else if( $offset == 0   )
		{
			 
			$this->session->unset_userdata('sess_user_name');
			$this->session->unset_userdata('sess_entry_date');
			$this->session->unset_userdata('sess_user_email');
			$this->session->unset_userdata('sess_user_phone');
			$this->session->unset_userdata('sess_user_city');
			$this->session->unset_userdata('sess_user_zip');
			$this->session->unset_userdata('sess_user_tag'); 
			$this->session->unset_userdata('sess_user_voc');
			$this->session->unset_userdata('sess_limit');
			$this->session->unset_userdata('sess_offset');

			$search_filter  =array(
			'username' => '',
			'createdOn'=> '',
			'user_email' => '',
			'user_phone' => '',
			'city' => '',
			'zip' => '',
			'tags' => '', 
			'vocations' =>'',
			'offset' => $offset , 
			'limit'	=> 10 
			); 
			
			$allmembers = $this->Members->get_all_members( $search_filter );   
			$pagedata['allmembers'] = $allmembers ; 
		}
		else if( $this->session->sess_user_name || $this->session->sess_entry_date || 
		$this->session->sess_user_email || $this->session->sess_user_phone || 
		$this->session->sess_user_city || $this->session->sess_user_zip || 
		$this->session->sess_user_tag || $this->session->sess_offset ||  $this->session->sess_limit  )
		{
			 
			$search_filter  =array(
			'username' => $this->session->sess_user_name ,
			'createdOn'=> $this->session->sess_entry_date  ,
			'user_email' => $this->session->sess_user_email ,
			'user_phone' => $this->session->sess_user_phone ,
			'city' => $this->session->sess_user_city ,
			'zip' =>$this->session->sess_user_zip ,
			'tags' =>  $this->session->sess_user_tag , 
			'vocations' => $this->session->sess_user_voc,
			'offset' => $offset, 
			'limit'	=> $this->session->sess_limit );
			
			 
			
			$member_search_filter  = array(
			'sess_user_name' => $this->session->sess_user_name ,
			'sess_entry_date'=> $this->session->sess_entry_date  ,
			'sess_user_email' => $this->session->sess_user_email ,
			'sess_user_phone' => $this->session->sess_user_phone ,
			'sess_user_city' => $this->session->sess_user_city ,
			'sess_user_zip' => $this->session->sess_user_zip ,
			'sess_user_tag' => $this->session->sess_user_tag , 
			'sess_user_voc' => $this->session->sess_user_voc,
			'sess_offset' => $offset, 
			'sess_limit'	=>$this->session->sess_limit); 
			 
			$allmembers = $this->Members->get_all_members($search_filter);   
			$pagedata['allmembers'] = $allmembers ;
			$this->session->set_userdata($member_search_filter);  
		}
		else 
		{
			$this->session->unset_userdata('sess_user_name');
			$this->session->unset_userdata('sess_entry_date');
			$this->session->unset_userdata('sess_user_email');
			$this->session->unset_userdata('sess_user_phone');
			$this->session->unset_userdata('sess_user_city');
			$this->session->unset_userdata('sess_user_zip');
			$this->session->unset_userdata('sess_user_tag'); 
			$this->session->unset_userdata('sess_user_voc');
			$this->session->unset_userdata('sess_offset');
			$this->session->unset_userdata('sess_limit'); 
			$search_filter  =array(
			'username' => '',
			'createdOn'=> '',
			'user_email' => '',
			'user_phone' => '',
			'city' => '',
			'zip' => '',
			'tags' => '', 
			'vocations' =>'',
			'offset' => $offset , 
			'limit'	=> 10 
			); 
			$allmembers = $this->Members->get_all_members( $search_filter );   
			$pagedata['allmembers'] = $allmembers ; 
		}
		
		if($this->input->post("hid_upload_excel") == 'upload')
		{
			$memberid = $this->input->post('hidliuserid');  
			$ds = DIRECTORY_SEPARATOR; 
			if (!empty($_FILES)) 
			{
				$this->load->model('MyExcelImportLog'); 
				$time = strtotime( date("Y-m-d H:i:s", time() ) );
				$tempFile = $_FILES['file']['tmp_name'];  
				$targetPath = $this->config->item('fileuploadpath') . $ds . "excel" . $ds ;
				$newfilename = 'linkedin_knows_' . $memberid . $time . "." .  pathinfo( $_FILES['file']['name'] , PATHINFO_EXTENSION); 
				$targetFile =  $targetPath .  $newfilename;   
				move_uploaded_file($tempFile,$targetFile); 
				$this->session->set_userdata(array('knows_file'=> $newfilename, 'admin_import_for' => $memberid ));  
				$this->MyExcelImportLog->add(array('user_id' => $memberid , 'filepath' => $newfilename));
			}
		}
		 
		
		if($this->input->post("linkedin_import") == "import")
		{
			 
			$ds = DIRECTORY_SEPARATOR;
			if(  $this->session->has_userdata('knows_file') && $this->session->has_userdata('admin_import_for') ): 
			
			$member_id = $this->session->has_userdata('admin_import_for');			
			$current_file = $this->session->knows_file;
			$targetPath = $this->config->item('fileuploadpath') . $ds . "excel" . $ds ;
			$file_path =  $targetPath . $current_file;
			  
			include_once ( $this->config->item('site_path')  .   'application/lib/Classes/PHPExcel/IOFactory.php' );
			$inputFileName = $file_path; 
			$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
			$sheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$arrayCount = count($sheet);  // Here get total count of row in that Excel sheet 

			$new =0;  
			$this->load->model("MyCommonVocations");
			$this->load->model("Knows");
			$this->load->model("UserAnswers"); 
			$this->load->model("MyLinkedinConnections");  
			$comvoc = $this->MyCommonVocations->get_common_vocations( $member_id  );
			$voc = $comvoc['common_vocs']; 
			$imported_knows=array(); 
			$existing_knows = $this->Knows->query("select distinct client_email from user_people where user_id='$member_id'"); 
			for($x=2 ; $x < 500 && $x <= $arrayCount;$x++)
			{
				$cname =   $sheet[$x]["A"] . " " . $sheet[$x]["B"] ; 
				$cname =  preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $cname);
				$cname =   html_entity_decode(  utf8_decode(   $cname ) );
				
				
				$email = $sheet[$x]["C"];
				$company = $sheet[$x]["D"];
				$profession = ($sheet[$x]["E"] !='' ? $sheet[$x]["E"] : " ")  ;
				$livestyle = ( isset($sheet[$x]["F"] ) ? $sheet[$x]["F"] : "") ; 
				if( trim($cname) == ""   ) break;  
				$newknow = array(   
				'user_id' => $member_id,  'client_name' => $cname, 
				'client_email' => $email, 'client_profession' => $profession , 
				'company' => $company,  'isimport' => '1' ,  
				'entrydate' => date('Y-m-d H:i:s') ,'isimported' => '0' ) ;  	
				$newknowdata = array(   
				'user_id' => $member_id,  'client_name' => $cname, 
				'client_email' => $email, 'client_profession' => $profession , 
				'company' => $company,  'isimport' => '1' ,  
				'entrydate' => date('Y-m-d H:i:s')  ) ;  
				$linkedinknow = array(   
				'userid' => $member_id,  'fullname' => $cname, 
				'email' => $email, 'profession' => $profession , 
				'company' => $company, 'entrydate' => date('Y-m-d H:i:s')  ) ;  	
				
				$found = 0;
				foreach ($existing_knows->result() as $erow)
				{
					if($erow->client_email == $email)
					{
						$found =1;
						break;
					}
				} 
				$imported_knows[$x-2]= $newknow ; 
				if($found == 0)
				{
					$knowid = $this->Knows->add_temporary($newknowdata);
					$this->MyLinkedinConnections->add($linkedinknow);
					$this->UserAnswers->add( 
					array ( 
						'question_id' => '9',  
						'user_id' =>  '$knowid', 
						'answer'=>   '$voc' 
						)  
					); 
					$newknow['isimported'] = $knowid; 
					$new++;
				}   	 	
			} 
			//updating file log 
			$this->load->model('MyExcelImportLog'); 
			$this->MyExcelImportLog->update_import_log
			(
				array(
					'last_row_processed' => $x,  
					'status' => '0', 
					'upload_date' => date('Y-m-d H:i:s'),
					'total_row' => $arrayCount ,
					'total_imported'=>  $new,
					'filepath'=> $current_file 
				) 
			);
			//remove session file 
			$this->session->unset_userdata('knows_file');
			$this->session->unset_userdata('admin_import_for');
			$pagedata['importedknows'] = $imported_knows;
			endif;	 
		}
		$pager_config['base_url'] = $this->config->item('base_url') . 'my-network/connections/';	 
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
		$this->load->view('member/manage',  $pagedata );
		$this->load->view('common/edit_know',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
		
	}
	 
	public function add()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		
		if($this->session->role == 'user')
		{
			redirect('/dashboard', 'refresh'); 
		} 
		
		$offset =  ( intval($this->uri->segment(2)) ?  $this->uri->segment(2) : 0 ) ; 
	 
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
		$pagedata['tags']  = $this->Tags->gettags();
		$this->load->model('MyCountry');  
		$pagedata['country']  = $this->MyCountry->get_all();
		
		
		if($this->input->post("add_member") == 'Submit')
		{
			$vocs = ($this->input->post('e_profession') != '' ? implode(",", $this->input->post('e_profession') ) : "");
			$knowtags = ($this->input->post('knowtags') != '' ? implode(",", $this->input->post('knowtags') ) : "");
			$groups = ($this->input->post('e_group') != '' ? implode(",", $this->input->post('e_group') ) : "");
			$target_voc = ($this->input->post('target_voc') != '' ? implode(",", $this->input->post('target_voc') ) : "");
			$new_member = array( 
				'username' => $this->input->post('e_name'), 
				'user_phone' =>$this->input->post('e_phone'), 
				'user_email' => $this->input->post('e_email'), 
				'tags' => $knowtags  ,
				'user_role' => 'user' 
			);
			$memberid = $this->Members->add( $new_member );
			 
			$this->load->model('Userdetails'); 
			$user_details = array( 
				'user_id' =>$memberid ,
				'street' => $this->input->post('e_street'), 
				'city' =>$this->input->post('e_location'), 
				'zip' => $this->input->post('e_zip'),  
				'groups' => $groups ,
				'target_clients' => $target_voc ,
				'vocations' => $vocs,
				'about_your_self' =>  $this->input->post('e_about')  
			);
			$this->Userdetails->save($user_details); 
			
			$this->session->set_userdata('msg_error', 'New Member Added!');
			
			redirect('/member/add', 'refresh'); 
				
		}  
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('member/add',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	
	public function nearby_members()
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
		$pagedata['profile_img'] = $this->config->item('profile_img');
		$pagedata['site_path'] = $this->config->item('site_path'); 
		$pagedata['title']  = "Nearby Members"; 
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
		$searchfilter = array('userid'=> $uid, 'offset' => 0, 'size' => 10  ); 
		 
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
			$this->load->model("Mailbox");
			$inbox = $this->Mailbox->send_mail_log($data);  
			$this->session->set_userdata( array('maillog'=>  $inbox['errmsg']  ) ); 
			redirect('/nearby-members', 'refresh'); 
		}
		
		$pagedata['connected_members']  = $this->Members->get_connected_members( $searchfilter ); 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('member/nearby', $pagedata); 
		$this->load->view('common/mail_composer',  $pagedata );
		$this->load->view('template/footer',   $pagedata); 
	}
	 
	  
	public function compose_email()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		
		if($this->session->role == 'user')
		{
			redirect('/dashboard', 'refresh'); 
		} 
		
		$this->load->model('Members'); 
		$memberid =  ( intval($this->uri->segment(3)) ?  $this->uri->segment(3) : 0 ) ; 
		if($memberid > 0)
		{
			$member_selected = $this->Members->getprofile( $memberid );
			$pagedata['member_selected']  = $member_selected;
			  
			if( $this->input->post("btn_send_email") == 'send')
			{
				$receipent_email  = $this->input->post("receipent_email");
				$member_row  = $member_selected->row();
				
				if($member_row->user_email != $receipent_email)
				{
					$this->session->set_userdata('msg_error', "Email sending failed!");
				}
				else 
				{
					$ds = DIRECTORY_SEPARATOR;  
					$path =  $this->config->item("site_path")  ;   
					if(  file_exists( $path . "templates/black_template_03.txt" ) )
					{
						$filecontent = file_get_contents( $path . "templates/black_template_03.txt" ) ;  
						$filecontent = str_replace("{mail_body}", $this->input->post("emailbody")  , $filecontent ) ;  
						$filecontent = str_replace("{partner}",  $member_row->username  , $filecontent ) ;  
						$filecontent = str_replace("{salutation}",  "MyCity" , $filecontent ) ;  
						$filecontent = str_replace("{year}", date('Y') , $filecontent ) ;  
						$mailbody  = $filecontent ; 
					}  
					send_email( $receipent_email , "referrals@mycity.com" , 
					"MyCity" ,  $this->input->post("compose_subject") ,   $mailbody 	  );
					
					$this->load->model('Mailbox');
					$data = array(
						'sender_id'  =>   $uid  ,  
						'receipent_id'  =>   $member_row->id  ,  
						'subject' =>   $this->input->post("compose_subject"),
						'emailbody' =>  $this->input->post("emailbody") ,
						'sender' =>  $this->session->email,
						'receipent' =>  $receipent_email ,  
						'senton' =>   date('Y-m-d H:i:s') 
					) ;
					$inbox = $this->Mailbox->add($data); 
					$this->session->set_userdata('msg_error', "Email send successfully!");
					redirect('/member/compose-email/' . $memberid , 'refresh');
					
				}  
			} 
		}
		else 
		{
			$pagedata['member_selected'] =null;
			$this->session->set_userdata('msg_error',  'Member not selected!' );
		} 
		  
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Compose Email";
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
		$pagedata['tags']  = $this->Tags->gettags();
		$this->load->model('MyCountry');  
		$pagedata['country']  = $this->MyCountry->get_all();
		  
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('member/compose_email',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	
	function switch_account()
	{
	 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		
		if($this->session->role == 'user')
		{
			redirect('/dashboard', 'refresh'); 
		} 
		$memberid =  ( intval($this->uri->segment(3)) ?  $this->uri->segment(3) : 0 ) ; 
		
		if($memberid > 0)
		{
			$this->load->model('Members');
			$loginlogrs = $this->Members->getprofile( $memberid ); 
			
			if($loginlogrs->num_rows() == 1 ) 
			{
				//clear previous cookie and session
				delete_cookie('_mcu');  
				$loginprofile = $this->Members->login_from_session( 
				array(   
				'id' => $loginlogrs->row()->id,
				'rememberme' => 1 ,
				'switcher' =>  'on' ) 
				);     
				$this->session->set_userdata( $loginprofile );    
				$cookie = array(
				'name'   => '_mcu', 
				'value'  => json_encode( $loginprofile ) ,
				'expire' => time()+86500, 
				'path'   => '/' 
				); 
				$this->input->set_cookie(   $cookie  );
				$log_err= '';
				 redirect('/dashboard', 'refresh');  
			} 
			else 
			{
				$log_err = 'Account switching failed!';
			}
		}   
	}
	
	
	
	public function check_duplicate_referrals()
	{ 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		
		if($this->session->role == 'user')
		{
			redirect('/dashboard', 'refresh'); 
		}  
		$mid =  ( intval($this->uri->segment(3)) ?  $this->uri->segment(3) : 0 ) ;  
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
		 
		  
		$this->load->model('Knows'); 
		
		if($mid > 0)
		{
			$duplicates = $this->Knows->get_duplicate_knows( array( 'mid' => $mid , 'offset' => 0) );
		}
		else 
		{
			$duplicates =null;
		}
		 
		$pagedata['duplicates'] = $duplicates; 
		$pager_config['base_url'] = $this->config->item('base_url') . 'my-network/connections/';	 
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
		$this->load->view('member/duplicate_knows',  $pagedata ); 
		$this->load->view('template/footer',   $pagedata);
		
	}
	 
	 
	 public function incomplete_signup()
	{ 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		
		if($this->session->role == 'user')
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
		$pagedata['title']  = "Manage Incomplete Signups";
		
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
		  
		$pagedata['members'] = $this->Members->get_incomplete_signup ( array('offset'=> $offset, 'limit' => 10)) ; 
		$pager_config['base_url'] = $this->config->item('base_url') . 'member/incomplete-signup' ;	 
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
		$this->load->view('member/incomplete_signup',  $pagedata ); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	function ratings_details()
	{
		//load rating details
		$pid = $this->input->post('id');
		$this->load->model('Myuserrating');	
		$ratings_details = $this->Myuserrating->get_rating_details(  $pid );
		$html ='';
		if($ratings_details->num_rows() > 0)
		{
			 
			$username ='';
			$head_html ='';
			foreach($ratings_details->result() as $ritem)
			{
				if($username == '')
				{
					$username = $ritem->username; 
					$head_html ='<div class="col-md-12"><hr/>';  
						$head_html .=   "<p class='text-center'><strong>Ratings below are given by " . $username . "</strong></p>" ;
					$head_html .='<hr/></div>';  
					$html .= $head_html;
				} 
				else 
					if($username != $ritem->username )
					{
						$username = $ritem->username; 
						$head_html = '<div class="col-md-12"><hr/>';  
						$head_html .=  "<p class='text-center'><strong>Ratings below are given by " . $username . "</strong></p>" ;
						$head_html .='<hr/></div>';   
						$html .= $head_html;
					}
					
				//star 
				$stars = '';
				for($i=0; $i<  $ritem->ranking; $i++)
				{
					$stars .= '<i class="fa fa-star orange"></i>';
				}
				 
				switch( $ritem->question_id )
				{
					case 1:
						$html .= "<div class='col-md-6'>Wants more business</div><div class='col-md-6'>" . $stars . "</div>"; 
						break;
					case 2:
						$html .="<div class='col-md-6'>Willing to Give Referrals</div><div class='col-md-6'>" . $stars . "</div>"; 
						break;
					case 3:
						$html .="<div class='col-md-6'>Expert Level in Their field</div><div class='col-md-6'>" . $stars . "</div>"; 
						break;
					case 4:
						$html .="<div class='col-md-6'>Would you refer</div><div class='col-md-6'>" . $stars . "</div>"; 
						break;
					case 5:
						$html .= "<div class='col-md-6'>Willing to Network</div><div class='col-md-6'>" . $stars . "</div> "; 
						break;
				}
				
				 
			}
			$html .= " </div>";
			
			$jsonresult = array('error' =>  '0' , 'results'=>$html,   'errmsg' =>  "No ratings found!"  ); 
		}
		else 
		{
			$jsonresult = array('error' =>  '1' ,  'results'=> '' , 'errmsg' =>  "No ratings found!"  ); 
			
		} 
		echo json_encode($jsonresult) ;
	}
	 
}
 
