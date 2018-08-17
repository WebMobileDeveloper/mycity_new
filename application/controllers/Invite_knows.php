<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invite_knows extends CI_Controller 
{
	function __construct() 
	{
		parent::__construct();
		$this->load->helper( array('form', 'url', 'cookie' ) );
		$this->load->library(array('session' ) );
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
		$this->load->model("MyInviteKnowLog");
		
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Invite to MyCity Landing Page Generator";  
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
		
		$allknows = null;
		$url =  "" ;
		if($this->input->post("btn_prepare") == "prepare_url")
		{
			$partnerid = $this->input->post("memberid");
			$knowid = $this->input->post("knowid"); 
			$knowname = $this->input->post("knowname");  
			$knowname_arr = explode('.', $knowname);
			$knowname =  implode(' ', array_filter( $knowname_arr  ) ) ;
			$knowname_arr = explode(',', $knowname);
			$knowname =  implode(' ', array_filter( $knowname_arr  ) ) ; 
			$knowname_arr = explode(' ', $knowname);
			$knowname =  implode('-', array_filter( $knowname_arr  ) ) ;
			
			$partnername = $this->input->post("partnername");  
			$partername_arr = explode('.', $partnername);
			$partnername =  implode(' ', array_filter( $partername_arr  ) ) ;
			$partername_arr = explode(',', $partnername);
			$partnername =  implode(' ', array_filter( $partername_arr  ) ) ; 
			$partername_arr = explode(' ', $partnername);
			$partnername =  implode('-', array_filter( $partername_arr  ) ) ; 
			
			$hash = md5($knowid.$partnerid);  
			$logid = $this->MyInviteKnowLog->add
			(
				array(  
					'know_id' => $knowid , 
					'partner_id' =>  $partnerid , 
					'know_name' => strtolower(  $knowname ),
					'partner_name' => strtolower(  $partnername ),
					'hash_id' =>$hash 
				)  
			);
			//send email
			$url = "https://mycity.com/profile/invite/" . $this->MyInviteKnowLog->get_url_by_id( $logid ); 
		}
		
		$pagedata['url'] =   $url  ; 
		$pagedata['allknows'] = $allknows; 
		$members = $this->Members->get_all_users_with_name();
		$pagedata['allmembers'] =$members; 
		$all_urls = $this->MyInviteKnowLog->get_all();
		$pagedata['all_urls'] = $all_urls ;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata);  
		$this->load->view('tools/generate_landing_page' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata);  
	} 
	 
	
	public function autocomplete_know_names()
	{
		$mid = $this->input->get("mid");  
		$this->load->model('Knows');  
		$json = $this->Knows->autocomplete_member_know_name_json(
		array(
		'mid' => $mid ,  
		'phrase' => $this->input->get('phrase') 
		)
		); 
	}
	
	
	
}
	
?>