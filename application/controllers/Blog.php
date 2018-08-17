<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends CI_Controller 
{
	function __construct() 
	{
		parent::__construct();
		$this->load->helper( array('form', 'url', 'cookie' ) );
		$this->load->library(array('session' ) );
    }
	
	public function index()
	{
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Blog";  
		$this->load->model('MyBlog');
		$pagedata['posts'] = $this->MyBlog->get_posts();
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('blog/index' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata); 
	}
	
	public function read()
	{
		$uri =  $this->uri->segment(2); 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['site'] = $this->config->item('site_url'); 
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Blog";  
		$this->load->model('MyBlog');
		$pagedata['posts'] = $this->MyBlog->get_single_post($uri);
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata);  
		$this->load->view('blog/read' ,   $pagedata); 
		$this->load->view('template/footer',   $pagedata);
		
	}
	
	
}
	
?>