<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {

function __construct() {
        parent::__construct();
        $this->load->helper( array(  'url', 'cookie' ) );
		$this->load->library(array('session'  ) ); 
    }  
	public function index()
	{
		delete_cookie('_mcu'); 
		$this->session->sess_destroy();
		redirect('/admin/logout.php', 'refresh'); 
	} 
}
