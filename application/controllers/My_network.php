<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('max_execution_time', 0); 

class My_network extends CI_Controller 
{
	function __construct() {
        parent::__construct(); 
		$this->load->library(array('session', 'pagination' ) );
		$this->load->helper( array('form', 'url', 'email' , 'array') ); 
    }
	
	public function index()
	{ 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id;  
		
		if(  $this->session->role =='admin' )
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
		
		$this->load->model('Knows');
		if( $this->input->post('btn_search') == 'search' )
		{
			 
			$search_filter  =array(
			'ref_name' => $this->input->post('srchRefName'),
			'entrydate'=> $this->input->post('srchentryDate'),
			'email' => $this->input->post('srchemail'),
			'phone' => $this->input->post('srchPhone'),
			'city' => $this->input->post('filtercity'),
			'srchZipCode' => $this->input->post('srchZipCode'),
			'tag' => $this->input->post('filterTags'),
			'lifestyle' => $this->input->post('filterLifestyle'),
			'locateVoc' => $this->input->post('locateVoc'),
			'offset' => 0, 'limit'	=> 10, 'uid' => $uid);  
			
			$know_search_filter  =array(
			'sref_name' => $this->input->post('srchRefName'),
			'sentrydate'=> $this->input->post('srchentryDate'),
			'semail' => $this->input->post('srchemail'),
			'sphone' => $this->input->post('srchPhone'),
			'scity' => $this->input->post('filtercity'),
			'ssrchZipCode' => $this->input->post('srchZipCode'),
			'stag' => $this->input->post('filterTags'),
			'slifestyle' => $this->input->post('filterLifestyle'),
			'slocateVoc' => $this->input->post('locateVoc'),
			'soffset' => $offset , 
			'slimit'	=> 10,
			'suid' => $uid);
			$src_result = $this->Knows->search_knows($search_filter);  
			$pagedata['knows']  = $src_result ; 
			$this->session->set_userdata($know_search_filter); 
		}
		else if( $offset == 0   )
		{
			
			$this->session->unset_userdata('sref_name');
			$this->session->unset_userdata('sentrydate');
			$this->session->unset_userdata('semail');
			$this->session->unset_userdata('sphone');
			$this->session->unset_userdata('scity');
			$this->session->unset_userdata('ssrchZipCode');
			$this->session->unset_userdata('stag');
			$this->session->unset_userdata('slifestyle');
			$this->session->unset_userdata('slocateVoc');
			$this->session->unset_userdata('soffset');
			$this->session->unset_userdata('slimit'); 
			if($this->session->role == 'user')
			{
				$knows = $this->Knows->get_myknows($uid, $offset, 10 );
				$pagedata['knows']  = $knows ;  
			}
		}
		else if( $this->session->sref_name || $this->session->sentrydate || 
		$this->session->semail || $this->session->sphone || 
		$this->session->scity || $this->session->ssrchZipCode || 
		$this->session->stag || $this->session->slifestyle || 
		$this->session->slocateVoc || $this->session->soffset || 
		$this->session->slimit  )
		{
			$search_filter  =array(
			'ref_name' => $this->session->sref_name ,
			'entrydate'=> $this->session->sentrydate  ,
			'email' => $this->session->semail ,
			'phone' => $this->session->sphone ,
			'city' => $this->session->scity ,
			'srchZipCode' =>$this->session->ssrchZipCode ,
			'tag' =>  $this->session->stag ,
			'lifestyle' => $this->session->slifestyle ,
			'locateVoc' => $this->session->slocateVoc,
			'offset' => $offset, 
			'limit'	=> $this->session->slimit,
			'uid' => $uid);
			
			$know_search_filter  =array(
			'sref_name' => $this->session->sref_name,
			'sentrydate'=> $this->session->sentrydate,
			'semail' => $this->session->semail ,
			'sphone' =>$this->session->sphone ,
			'scity' => $this->session->scity ,
			'ssrchZipCode' => $this->session->ssrchZipCode ,
			'stag' => $this->session->stag ,
			'slifestyle' => $this->session->slifestyle ,
			'slocateVoc' => $this->session->slocateVoc,
			'soffset' => $offset , 
			'slimit'	=> $this->session->slimit,
			'suid' => $uid);
			$src_result = $this->Knows->search_knows($search_filter);  
			$pagedata['knows']  = $src_result ; 
			$this->session->set_userdata($know_search_filter);
			
		}
		else 
		{
			$this->session->unset_userdata('sref_name');
			$this->session->unset_userdata('sentrydate');
			$this->session->unset_userdata('semail');
			$this->session->unset_userdata('sphone');
			$this->session->unset_userdata('scity');
			$this->session->unset_userdata('ssrchZipCode');
			$this->session->unset_userdata('stag');
			$this->session->unset_userdata('slifestyle');
			$this->session->unset_userdata('slocateVoc');
			$this->session->unset_userdata('soffset');
			$this->session->unset_userdata('slimit');
			$knows = $this->Knows->get_myknows($uid, $offset, 10 );
			$pagedata['knows']  = $knows ;
 
		} 
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
		 
		//load mail templates
		$this->load->model('MyMailTemplates'); 
		$pagedata['mailtemplates'] = $this->MyMailTemplates->get_templates();
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
		$this->load->view('know/index',  $pagedata );
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
		$pagedata['lifestyles']  = $this->Lifestyles->getlifestyles(); 
		$this->load->model('Groups');  
		$pagedata['groups']  = $this->Groups->getgroups(); 
		$this->load->model('Questions');  
		$pagedata['questions']  = $this->Questions->getquestions();
		$this->load->model('Tags');  
		$pagedata['tags']  = $this->Tags->gettags();
		
		
		$this->load->model('MyCommonVocations');
		$com_vocs = $this->MyCommonVocations->get_common_vocation_for_member($uid);
		
		$pagedata['comvoc'] = $com_vocs['common_vocs'];
		 
 
		if($this->session->role == 'user')
		{
			$this->load->model('Knows');
			$knows = $this->Knows->get_myknows($uid, 0, 10 );
			$pagedata['knows']  = $knows ;  
		}
		
		if($this->input->post("add_know") == 'Submit')
		{
			$vocs = ($this->input->post('e_profession') != '' ? implode(",", $this->input->post('e_profession') ) : "");
			$knowtags = ($this->input->post('knowtags') != '' ? implode(",", $this->input->post('knowtags') ) : "");
			$e_lifestyle = ($this->input->post('e_lifestyle') != '' ? implode(",", $this->input->post('e_lifestyle') ) : "");
			$e_location = ($this->input->post('e_location') != '' ? implode(",", $this->input->post('e_location') ) : "");
			
			$new_know = array(
				'user_id' => $uid, 
				'client_name' => $this->input->post('e_name'),
				'client_profession' => $vocs,
				'client_phone' =>$this->input->post('e_phone'), 
				'client_email' => $this->input->post('e_email'),
				'client_lifestyle' => $e_lifestyle, 
				'client_location' =>  $e_location,  
				'client_zip' => $this->input->post('e_zip'),
				'client_note' => $this->input->post('e_note'), 
				'tags' => $knowtags   
			);
			$knowid = $this->Knows->add($new_know); 
			$rate2 =   $this->input->post('rating2') ;
			$rate3 =   $this->input->post('rating3') ;
			$rate4 =  $this->input->post('rating4') ;
			$rate5 =   $this->input->post('rating5') ;
			
			if($knowid > 0)
			{
				$rate1 = ( $this->input->post('rating1')? $this->input->post('rating1') : 0);
				$user_rate = array 
				(
					array( 'user_id' => $knowid, 'question_id' =>  1,  'ranking' =>  $rate1 ),
					array( 'user_id' => $knowid, 'question_id' =>  2,  'ranking' =>  $rate2 ),
					array( 'user_id' => $knowid, 'question_id' =>  3,  'ranking' =>  $rate3 ),
					array( 'user_id' => $knowid, 'question_id' =>  4,  'ranking' =>  $rate4 ),
					array( 'user_id' => $knowid, 'question_id' =>  5,  'ranking' =>  $rate5 )
				); 
				$this->load->model('Ratings');
				$this->Ratings->add_batch($user_rate); 
				$this->load->model('UserAnswers');
				$user_answer = array( 
				'user_id' => $knowid, 'question_id' =>$this->input->post('questionid'), 
				'answer' => $this->input->post('voc_answer') );
				$this->UserAnswers->add($user_answer);
			}
		}
		
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('know/add',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	} 
	
	public function wizard()
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
		$this->load->model('Knows');
		if($this->session->role == 'user')
		{ 
			$knows = $this->Knows->get_myknows($uid, 0, 10 );
			$pagedata['knows']  = $knows ;
		}
		$pagedata['wizard'] = 'true'; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('know/index',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	 
	public function autocomplete_name()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		$role = $this->session->role;
		$this->load->model('Knows');
		$json = $this->Knows->autocomplete_json(array('user_id' => $uid , 'user_role'=>  $role, 'phrase' => $this->input->get('phrase') ));
	  
	} 
	 
	public function autocomplete_left_name()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id; 
		$role = $this->session->role;
		$this->load->model('Knows');
		$json = $this->Knows->autocomplete_my_know_name_json(array('user_id' => $uid , 'user_role'=>  $role, 'phrase' => $this->input->get('phrase') ));
	  
	} 
	
	 
	
	public function import_from_linkedin()
	{ 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		$this->load->model('Knows'); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Import LinkedIn Contacts";
		
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
		 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		if($this->input->post("btn_update_photo") == "change_photo" )
		{
			$filename ='user_' . $reg_id ; 
			$config['upload_path']          =  $this->config->item('uploadpath');  
			$config['allowed_types']        = 'xls';
			$config['max_size']             = 0;
			$config['max_width']            = 0;
			$config['max_height']           = 0;
			$config['file_name'] =  $filename ;
			
			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('usrImg'))
			{
				$error = array('error' => $this->upload->display_errors());
				$this->load->view('register/choose_photo', $error);
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());
				$this->load->model("Members");
				$this->Members->update( array('image' => $filename.'.jpg' ) , $reg_id );
				$loginprofile = $this->Members->login_from_session( array('id' =>$reg_id, 'rememberme' => 1 ));
				$pagedata['login']  = $loginprofile;
				if($loginprofile['id'] != 0)
				{
					//clear registration session variable here
					$this->session->unset_userdata('reg_id');  
					$this->session->unset_userdata('new_signup'); 
					$this->session->set_userdata( $loginprofile );
					redirect('dashboard', 'refresh');
				} 	   
			}
		} 
		$this->load->view('know/linkedin_import_delayed_method',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	
	public function file_upload()
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id;  
		$ds = DIRECTORY_SEPARATOR; 
		if (!empty($_FILES)) 
		{
			$this->load->model('MyExcelImportLog'); 
			$time = strtotime( date("Y-m-d H:i:s", time() ) );
			$tempFile = $_FILES['file']['tmp_name'];  
			$targetPath = $this->config->item('fileuploadpath') . $ds . "excel" . $ds ;
			$newfilename = 'linkedin_knows_' . $uid . $time . "." .  pathinfo( $_FILES['file']['name'] , PATHINFO_EXTENSION); 
			$targetFile =  $targetPath .  $newfilename;   
			move_uploaded_file($tempFile,$targetFile); 
			$this->session->set_userdata(array('knows_file'=> $newfilename)); 
			$this->MyExcelImportLog->add(array('user_id' => $uid , 'filepath' => $newfilename));
		} 
	}
	
	public function import( )
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id;
		
		
		$ds = DIRECTORY_SEPARATOR; 
		if(  $this->session->has_userdata('knows_file') ):
		 
		$targetPath = $this->config->item('fileuploadpath') . $ds . "excel" . $ds ;
		$file_path =  $targetPath . $this->session->knows_file;
		include_once ( $this->config->item('site_path')  .   'application/lib/Classes/PHPExcel/IOFactory.php' );
		$inputFileName = $file_path; 
		$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
		$sheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$arrayCount = count($sheet);  // Here get total count of row in that Excel sheet
		$new =0; 
		$voc='';
		$this->load->model("Settings");
		$this->load->model("Knows");
		$this->load->model("Useranswers"); 
		$this->load->model("MyLinkedinConnections"); 
		
		$comvoc = $this->Settings->get_config("common_vocation" );
		if($comvoc->num_rows()  > 0)
		{
			$voc = $comvoc->row()->svalue ;
		}
		$imported_knows=array();
		 
		$existing_knows = $this->Knows->query("select client_email from user_people where user_id='$uid'");
		  
		for($x=2;$x<=$arrayCount;$x++)
		{  
			$cname =  $sheet[$x]["A"] . " " . $sheet[$x]["B"];
			$email = $sheet[$x]["C"];
			$company = $sheet[$x]["D"];
			$profession = ($sheet[$x]["E"] !='' ? $sheet[$x]["E"] : " ")  ;
			$livestyle = ( isset($sheet[$x]["F"] ) ? $sheet[$x]["F"] : "") ; 
			if( trim($cname) == ""   ) break; 
			
			$newknow = array(   
			'user_id' => $uid,  'client_name' => $cname, 
			'client_email' => $email, 'client_profession' => $profession , 
			'company' => $company,  'isimport' => '1' ,  
			'entrydate' => date('Y-m-d H:i:s') ,'isimported' => '0' ) ;  	
			$newknowdata = array(   
			'user_id' => $uid,  'client_name' => $cname, 
			'client_email' => $email, 'client_profession' => $profession , 
			'company' => $company,  'isimport' => '1' ,  
			'entrydate' => date('Y-m-d H:i:s')  ) ;  
			$linkedinknow = array(   
			'userid' => $uid,  'fullname' => $cname, 
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
			if($found ==0)
			{
				$knowid = $this->Knows->add($newknowdata); 
				$this->MyLinkedinConnections->add($linkedinknow);					
				$this->UserAnswers->add( array ( 
				'question_id' => '9',  
				'user_id' =>  '$knowid', 'answer'=>   '$voc' )  ); 
				$newknow['isimported'] = $knowid; 
				$new++;
			}   	 	
		}
		 
		//remove session file 
		$this->session->unset_userdata('knows_file');
		$pagedata['importedknows'] = $imported_knows;
		endif; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Import New Knows"; 
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
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('know/importedlist_defered',  $pagedata );
		$this->load->view('template/footer',   $pagedata); 
	}   	
	public function search( )
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id; 
		$result='';		
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Import New Knows"; 
		$this->load->model('Members'); 
		$pagedata['member']  = $this->Members->getprofile( $uid );
		$this->load->model('Helpbuttons');    
		$helpbuttons = $this->Helpbuttons->getbuttons(); 
		$button_array = array();
		foreach($helpbuttons->result() as $row)
		{
			array_push($button_array,  array('id'=> $row->id, 
			'helptitle' => $row->helptitle, 
			'helpvideo' =>  $row-> helpvideo )  ); 
		}
		
		if($this->input->post("btn_search_knows") == "search_filter")
		{
			$this->load->model('Knows');
			$searchfilter = array('name' => $this->input->post("src_name"), 
			'vocation' => $this->input->post("src_vocation")	);
			
			 
			
			$result = $this->Knows->get_knows($searchfilter, 1, 10); 
		}  
		$pagedata['result'] = $result;
		$pagedata['help_data_buttons']  = $button_array; 
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('know/search',  $pagedata );
		$this->load->view('template/footer',   $pagedata); 
	}
	
	public function my_connections( )
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id; 
		$offset =  ( intval($this->uri->segment(3)) ?  $this->uri->segment(3) : 0 ) ;   
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['profile_img'] = $this->config->item('profile_img'); 
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Manage My Connections"; 
		$this->load->model('Members'); 
		$pagedata['member']  = $this->Members->getprofile( $uid );
		$this->load->model('Helpbuttons');    
		$helpbuttons = $this->Helpbuttons->getbuttons();
		
		$button_array = array();
		foreach($helpbuttons->result() as $row)
		{
			array_push($button_array,  array('id'=> $row->id, 
			'helptitle' => $row->helptitle, 
			'helpvideo' =>  $row-> helpvideo )  ); 
		}
		
		if($this->input->post("btn_search_knows") == "search_filter")
		{
			$this->load->model('Knows');
			$searchfilter = array('name' => $this->input->post("src_name"), 
			'vocation' => $this->input->post("src_vocation")	);
			$result = $this->Knows->get_knows($searchfilter, 0, 10);
			$pagedata['result'] = $result;
		}
		
		$pagedata['help_data_buttons']  = $button_array;  
		$this->load->model("Memberconnection"); 
		$search_filter = array( 
			'userid' => $uid  ,   
			'offset'=> $offset,      
			'rstatus'=> 1 ,     
		    'pagesize'=>  10   
		  );
		   
		$connections = $this->Memberconnection->get_all_connections ($search_filter);
		$pagedata['connections'] = $connections; 
	
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
			 redirect('/my-network/connections', 'refresh');  
		}
 
 
		$pager_config['base_url'] = $this->config->item('base_url') . 'my-network/connections';	 
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
		$this->load->view('know/my_connections',  $pagedata );
		$this->load->view('common/mail_composer',  $pagedata );
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	public function requests_received( )
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		}
		$uid = $this->session->id;
		$this->load->model("Memberconnection"); 
		
		$offset2 =  ( intval($this->uri->segment(3)) ?  $this->uri->segment(3) : 0 ) ;  
		
		$this->load->model("MyAutoConnectLog");
		$autoconnectlist = $this->MyAutoConnectLog->get_autoconnect_list($uid);
		
		if($autoconnectlist->num_rows() > 0)
		{
			foreach($autoconnectlist->result_array() as $row )
			{
				//insert into connection
				 
				$this->Memberconnection->add( array( 
				'firstpartner' => $row['sender'],
				'secondpartner' => $row['receipent'],
				'request_type' => '1',
				'requestdate' => $row['cdate'],
				'approvedon' => date('Y-m-d H:i:s') ,
				'status' => 1 )  );  
			}
		} 
		
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['profile_img'] = $this->config->item('profile_img'); 
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Connection Request Received"; 
		$this->load->model('Members'); 
		$pagedata['member']  = $this->Members->getprofile( $uid );
		$this->load->model('Helpbuttons');    
		$helpbuttons = $this->Helpbuttons->getbuttons();
		
		$button_array = array();
		foreach($helpbuttons->result() as $row)
		{
			array_push($button_array,  array('id'=> $row->id, 
			'helptitle' => $row->helptitle, 
			'helpvideo' =>  $row-> helpvideo )  ); 
		}
		
		if($this->input->post("btn_search_knows") == "search_filter")
		{
			$this->load->model('Knows');
			$searchfilter = array('name' => $this->input->post("src_name"), 
			'vocation' => $this->input->post("src_vocation")	);
			$result = $this->Knows->get_knows($searchfilter, 0, 10);
			$pagedata['result'] = $result;
		} 
		$pagedata['help_data_buttons']  = $button_array;  
		 
		$search_filter = array( 
		'userid' => $uid  ,   
		 'offset'=> $offset2,      
		  'rstatus'=>  -1 ,   
		   'dir'=> 0,   
		   'pagesize'=> 10 
		);
		$connections = $this->Memberconnection->get_all_connections ($search_filter);
		$pagedata['connections2'] = $connections;
		$pagedata['direction2'] =  0; 
		$pager_config['base_url'] = $this->config->item('base_url') . 'connection/my_connections/';	 
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
		$this->load->view('know/connections_requests_received_unused',  $pagedata );
		$this->load->view('common/mail_composer',  $pagedata );
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	public function members()
	{ 
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id;  
		
		if(  $this->session->role =='user' )
		{
			redirect('/my-network', 'refresh'); 
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
		
		$this->load->model('Knows');
		if( $this->input->post('btn_search') == 'search' )
		{ 
			$search_filter  =array(
			'ref_name' => $this->input->post('srchRefName'),
			'entrydate'=> $this->input->post('srchentryDate'),
			'email' => $this->input->post('srchemail'),
			'phone' => $this->input->post('srchPhone'),
			'city' => $this->input->post('filtercity'),
			'srchZipCode' => $this->input->post('srchZipCode'),
			'tag' => $this->input->post('filterTags'),
			'lifestyle' => $this->input->post('filterLifestyle'),
			'locateVoc' => $this->input->post('locateVoc'),
			'offset' => 0, 'limit'	=> 10, 'uid' => $uid);  
			$know_search_filter  =array(
			'sref_name' => $this->input->post('srchRefName'),
			'sentrydate'=> $this->input->post('srchentryDate'),
			'semail' => $this->input->post('srchemail'),
			'sphone' => $this->input->post('srchPhone'),
			'scity' => $this->input->post('filtercity'),
			'ssrchZipCode' => $this->input->post('srchZipCode'),
			'stag' => $this->input->post('filterTags'),
			'slifestyle' => $this->input->post('filterLifestyle'),
			'slocateVoc' => $this->input->post('locateVoc'),
			'soffset' => 0 , 
			'slimit'	=> 10,
			'suid' => $uid); 
			 
			$src_result = $this->Knows->search_knows($search_filter);  
			$pagedata['knows']  = $src_result ; 
			$this->session->set_userdata($know_search_filter); 
		}
		else if( $offset == 0   )
		{
			$this->session->unset_userdata('sref_name');
			$this->session->unset_userdata('sentrydate');
			$this->session->unset_userdata('semail');
			$this->session->unset_userdata('sphone');
			$this->session->unset_userdata('scity');
			$this->session->unset_userdata('ssrchZipCode');
			$this->session->unset_userdata('stag');
			$this->session->unset_userdata('slifestyle');
			$this->session->unset_userdata('slocateVoc');
			$this->session->unset_userdata('soffset');
			$this->session->unset_userdata('slimit'); 
			if($this->session->role == 'user')
			{
				$knows = $this->Knows->get_myknows($uid, $offset, 10 );
				$pagedata['knows']  = $knows ;  
			}
		}
		else if( $this->session->sref_name || $this->session->sentrydate || 
		$this->session->semail || $this->session->sphone || 
		$this->session->scity || $this->session->ssrchZipCode || 
		$this->session->stag || $this->session->slifestyle || 
		$this->session->slocateVoc || $this->session->soffset || 
		$this->session->slimit  )
		{
			 
			$search_filter  =array(
			'ref_name' => $this->session->sref_name ,
			'entrydate'=> $this->session->sentrydate  ,
			'email' => $this->session->semail ,
			'phone' => $this->session->sphone ,
			'city' => $this->session->scity ,
			'srchZipCode' =>$this->session->ssrchZipCode ,
			'tag' =>  $this->session->stag ,
			'lifestyle' => $this->session->slifestyle ,
			'locateVoc' => $this->session->slocateVoc,
			'offset' => $this->session->soffset, 
			'limit'	=> $this->session->slimit,
			'uid' => $uid); 
			$src_result = $this->Knows->search_knows($search_filter);  
			$pagedata['knows']  = $src_result ;   
		}
		else 
		{
			$this->session->unset_userdata('sref_name');
			$this->session->unset_userdata('sentrydate');
			$this->session->unset_userdata('semail');
			$this->session->unset_userdata('sphone');
			$this->session->unset_userdata('scity');
			$this->session->unset_userdata('ssrchZipCode');
			$this->session->unset_userdata('stag');
			$this->session->unset_userdata('slifestyle');
			$this->session->unset_userdata('slocateVoc');
			$this->session->unset_userdata('soffset');
			$this->session->unset_userdata('slimit');
			 
			$knows = $this->Knows->get_myknows($uid, $offset, 10 );
			$pagedata['knows']  = $knows ;

			
		} 
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
		 
		//load mail templates
		$this->load->model('MyMailTemplates'); 
		$pagedata['mailtemplates'] = $this->MyMailTemplates->get_templates();
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
		$this->load->view('know/index',  $pagedata );
		$this->load->view('common/edit_know',  $pagedata );
		$this->load->view('template/footer',   $pagedata);
	}
	
	public function import_delayed( )
	{
		if( !$this->session->has_userdata('id') )
		{
			redirect('/login', 'refresh'); 
		} 
		$uid = $this->session->id;
		 
		$ds = DIRECTORY_SEPARATOR; 
		if(  $this->session->has_userdata('knows_file') ):
		
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
		$comvoc = $this->MyCommonVocations->get_common_vocations( $uid  );
		$voc = $comvoc['common_vocs']; 
		$imported_knows=array(); 
		$existing_knows = $this->Knows->query("select distinct client_email from user_people where user_id='$uid'"); 
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
			'user_id' => $uid,  'client_name' => $cname, 
			'client_email' => $email, 'client_profession' => $profession , 
			'company' => $company,  'isimport' => '1' ,  
			'entrydate' => date('Y-m-d H:i:s') ,'isimported' => '0' ) ;  	
			$newknowdata = array(   
			'user_id' => $uid,  'client_name' => $cname, 
			'client_email' => $email, 'client_profession' => $profession , 
			'company' => $company,  'isimport' => '1' ,  
			'entrydate' => date('Y-m-d H:i:s')  ) ;  
			$linkedinknow = array(   
			'userid' => $uid,  'fullname' => $cname, 
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
				$this->UserAnswers->add( array ( 
				'question_id' => '9',  
				'user_id' =>  '$knowid', 'answer'=>   '$voc' )  ); 
				$newknow['isimported'] = $knowid; 
				$new++;
			}   	 	
		}
		
		//updating file log 
		$this->load->model('MyExcelImportLog'); 
		$this->MyExcelImportLog->update_import_log( 
		array( 
		'last_row_processed' => $x, 
		'status' => '0', 
		'upload_date' => date('Y-m-d H:i:s'),
		'total_row' => $arrayCount ,
		'total_imported'=>  $new,
		'filepath'=> $current_file ) 
		);
		
		//remove session file 
		$this->session->unset_userdata('knows_file');
		$pagedata['importedknows'] = $imported_knows;
		endif; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Import New Knows"; 
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
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('know/importedlist_defered',  $pagedata );
		$this->load->view('template/footer',   $pagedata); 
	}  
}
