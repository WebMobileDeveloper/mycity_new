<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testimonials extends CI_Controller 
{
	function __construct() 
	{
		parent::__construct();
		$this->load->helper( array('form', 'url', 'email', 'cookie' ) );
		$this->load->library(array('session', 'form_validation' ) );
    }
	
	public function index()
	{
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Testimonials";  
		$this->load->model('MyTestimonials');
		$pagedata['testimonials'] = $this->MyTestimonials->get_video_testimonials();
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('testimonials' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	} 
	
	public function manage()
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
		 
		
		$this->load->model('MyTestimonials'); 
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
		
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Manage Testimonials";  
		
		if($this->input->post("btn_save_tmv") == "save")
		{
			$video_url = $this->input->post('testimonial_video');
			$video_summary = $this->input->post('testimonial_summary'); 
			$edit_id = $this->input->post('edit_id');
			
			if($edit_id > 0)
			{
				$testimonialid = $this->MyTestimonials->update( array( 'videolink' => $video_url , 'summary' => $video_summary ), $edit_id );
			 
				if($testimonialid > 0)
				{
					$this->session->set_userdata("msg_error", "Video testimonial saved!");
				} 
			}
			else 
			{
				$testimonialid = $this->MyTestimonials->add( array( 'videolink' => $video_url , 'summary' => $video_summary, 'printorder' => 999) ); 		
				if($testimonialid > 0)
				{
					$this->session->set_userdata("msg_error", "Video testimonial saved!");
				}
				else 
				{
					$this->session->set_userdata("msg_error", "Video testimonial could not be saved!");
				}
			}
			
			redirect( current_url(), 'refresh'); 
		} 
		if($this->input->post("save_sorting") == "save")
		{
			$ids = array_filter( explode(",", $this->input->post('tm_ids') ) ); 
			$updatecount = $this->MyTestimonials->update_sort_order($ids); 		
			
			if($updatecount > 0)
			{
				$this->session->set_userdata("msg_error_upd", "Testimonial display order updated!");
			}
			else 
			{
				$this->session->set_userdata("msg_error_upd", "Testimonial display order could not be updated!");
			}
			redirect( current_url(), 'refresh'); 
		}
		
		if($this->input->post("del_tmv") == "delete")
		{
			$id  =   $this->input->post('del_id')  ; 
			$this->MyTestimonials->remove($id);
			$this->session->set_userdata("msg_error_upd", "Testimonial removed!"); 
			redirect( current_url(), 'refresh'); 
		}
		
		$edit_id =  ( intval($this->uri->segment(4)) ?  $this->uri->segment(4) : 0 ) ;
		$mode =  (  $this->uri->segment(3)  ?  $this->uri->segment(3) : 0 ) ;
		$pagedata['edit_testimonial'] = null;
		if($mode =='change' && $edit_id > 0)
		{
			$edit_testimonial= $this->MyTestimonials->get_video_testimonial_by_id($edit_id); 
			$pagedata['edit_testimonial'] = $edit_testimonial; 
		}
		 
		$pagedata['cur_url'] = 'tmv'; //testimonial video 
		$pagedata['testimonials'] = $this->MyTestimonials->get_video_testimonials();
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);   
		$this->load->view('template/common_header',   $pagedata); 
		$this->load->view('template/navigation_side',   $pagedata); 
		$this->load->view('testimonials/manage',   $pagedata);   
		$this->load->view('template/footer',   $pagedata); 
	} 
}
	
?>