<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tools  extends CI_Controller 
{
	function __construct() {
        parent::__construct();
     
    } 
	
	public function send_email()
	{ 
		//$inbox = $this->Mailbox->send_mail_log($data); 
		echo "Send email"; 
	} 
	public function cron_import_knows( )
	{
		
		$ds = DIRECTORY_SEPARATOR; 
		include_once ( $this->config->item('site_path')  .   'application/lib/Classes/PHPExcel/IOFactory.php' );
		$this->load->model("MyCommonVocations");
		$this->load->model("Knows");
		$this->load->model("UserAnswers"); 
		$this->load->model("MyLinkedinConnections");  
		 
		$this->load->model('MyExcelImportLog');
		$all_files = $this->MyExcelImportLog->get_files('0') ;
		 
		foreach($all_files->result() as $file)
		{ 
			$uid = $file->user_id;
			$comvoc = $this->MyCommonVocations->get_common_vocations( $uid  );
			$voc = $comvoc['common_vocs']; 
		
			$current_file = $file->filepath;
			$targetPath = $this->config->item('fileuploadpath') . $ds . "excel" . $ds ;
			$file_path =  $targetPath . $current_file; 
			$inputFileName = $file_path; 
			$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
			$sheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$arrayCount = count($sheet);   
			$new =0;  
			$startoffset = ( $file->last_row_processed == 0 ? 2 : ($file->last_row_processed + 1 )  ) ;
			 
			$imported_knows=array(); 
			$existing_knows = $this->Knows->query("select client_email from user_people where user_id='$uid'"); 
			for($x = $startoffset ; $x < ($startoffset + 50) && $x <= $arrayCount;$x++)
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
			'filepath'=> $current_file ) 
			);
			
			 
		} 
		$this->load->view('cron_page' );  
	}


	public function update_profile_shortcode()
	{
		$this->load->model('Members');  
		$mid = $this->Members->get_all_users(); 
		foreach($mid->result() as $row)
		{ 
			$uid = $row->id ;
			if( $row->user_shortcode == '')
			{
				echo "ID:" . $uid . "<br/>"; 
				$this->Members->update_url( $data = array('id' => $uid ) ); 
			}			
		} 
	}

	 
}
