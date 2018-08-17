<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

function __construct() {
        parent::__construct();
        $this->load->helper( array('form', 'url', 'email' , 'array') );
		$this->load->library(array('session', 'form_validation' ) );  
    }
	
	 
	public function index() 
	{
		if( !$this->session->has_userdata('new_signup') )
		{
			redirect('/', 'refresh'); 
		} 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "User Registration";
		
		$pagedata['new_reg_email']  = $this->session->new_signup;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$pagedata['error_msg'] ='';
		if($this->input->post("btn_updatename") == "create_account" )
		{
			$this->form_validation->set_rules('first_name', 'First Name', 'required' );
			$this->form_validation->set_rules('last_name', 'Last Name', 'required' );
			$this->form_validation->set_rules('password', 'Password', 'required' ); 
			
			$first_name = $this->input->post("first_name");
			$last_name = $this->input->post("last_name");
			$password = $this->input->post("password");
			$email = $this->input->post("email2");
			
			if ($this->form_validation->run() == FALSE)
			{
				$pagedata['error_msg'] = 'There where missing fields' ;
				$this->load->view('register/about_you', $pagedata);
			}
			else 
			{
				$name = $first_name  ;
				if($last_name !='')
					$name .=   " " . $last_name;
				$data = array('username' => $name,   'user_email'=> $email , 'user_pass' =>  md5($password) );  
				$this->load->model("Members");
				
				if($this->Members->check_duplicate($email) )
				{
					$pagedata['errmsg'] = 'Email you provided has an account already.';
					$this->load->view('register', $pagedata); 
				}
				else 
				{
					$reg_id = $this->Members->add($data); 
					$new_signup_id = array( 'reg_id' =>  $reg_id, 'passwd' =>$password  );
					$this->session->set_userdata( $new_signup_id );  				
					$this->load->view('register/registration_complete', $pagedata);
				}  
			}
		} 
		else 
		{
			$this->load->view('register/about_you', $pagedata);
		} 	
		$this->load->view('template/footer',   $pagedata);  
	}
	 
	public function about_yourself() 
	{
		if( !$this->session->has_userdata('reg_id') )
		{
			redirect('/', 'refresh'); 
		}
		
		$reg_id = $this->session->reg_id; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "About Yourself"; 
		$pagedata['new_reg_email']  = $this->session->new_signup;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$pagedata['error_msg'] ='';
		if($this->input->post("regUserAddress") == "save_address" )
		{
			$this->form_validation->set_rules('country', 'Country', 'required' );
			$this->form_validation->set_rules('street', 'Street', 'required' );
			$this->form_validation->set_rules('city', 'City', 'required' );   
			$this->form_validation->set_rules('zip', 'Zip', 'required' ); 
			
			$country = $this->input->post("country");
			$street = $this->input->post("street");
			$city = $this->input->post("city");
			$zip = $this->input->post("zip");
			$province = $this->input->post("province");
			 
			if ($this->form_validation->run() == FALSE)
			{
				$pagedata['error_msg'] = 'You haven\'t fill some mandatory fields!' ;
				$this->load->view('register/address_details', $pagedata);
			}
			else 
			{
				$data = array('street' => $street, 
					'country'=> $country ,
					'city' =>   $city, 
					'zip' =>   $zip,  
					'user_id' =>   $reg_id
					);   
				$this->load->model("Userdetails");  
				$this->Userdetails->save($data);
				redirect('register/your_vocations', 'refresh');  
			}
		}
		else 
		{
			$this->load->view('register/address_details', $pagedata);
		} 	
		$this->load->view('template/footer',   $pagedata);  
	} 
	
	
	
	public function your_vocations() 
	{
		if( !$this->session->has_userdata('reg_id') )
		{
			redirect('/', 'refresh'); 
		}
		
		$reg_id = $this->session->reg_id; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Your Vocations"; 
		$pagedata['new_reg_email']  = $this->session->new_signup;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$pagedata['error_msg'] ='';
		if($this->input->post("regvocations") == "save_vocations" )
		{
		 
			$voc_1 = $this->input->post("interests[1]");
			$voc_2 = $this->input->post("interests[2]");
			$voc_3 = $this->input->post("interests[3]");
			if($voc_1 !='')
			{
				$vocations = $voc_1;
			}
			if($voc_2 !='')
			{
				$vocations .= ",". $voc_2;
			}
			if($voc_3 !='')
			{
				$vocations .= ",". $voc_3;
			} 
			$data = array('vocations' => $vocations  );   
			$this->load->model("Userdetails");  
			$this->Userdetails->update_vocation($data, $reg_id); 
			redirect('register/your_cities', 'refresh');   
			
		}
		else 
		{
			$this->load->view('register/select_vocations', $pagedata);
		} 	
		$this->load->view('template/footer',   $pagedata);  
	}
	
	
	public function your_cities() 
	{
		if( !$this->session->has_userdata('reg_id') )
		{
			redirect('/', 'refresh'); 
		}
		
		$reg_id = $this->session->reg_id; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Your Cities"; 
		$pagedata['new_reg_email']  = $this->session->new_signup;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$pagedata['error_msg'] ='';
		if($this->input->post("btn_save_group") == "save_group" )
		{
			$groups = $this->input->post('groups[]', TRUE);
			$groups_csv = array('groups' => implode(', ',  $groups ) ); 
			$this->load->model("Userdetails");  
			$this->Userdetails->update($groups_csv, $reg_id);  
			redirect('register/your_target_clients', 'refresh');   
		}
		else 
		{
			$this->load->view('register/your_cities', $pagedata);
		}
		$this->load->view('template/footer',   $pagedata);  
	} 
	
	
	public function your_target_clients() 
	{
		if( !$this->session->has_userdata('reg_id') )
		{
			redirect('/', 'refresh'); 
		}
		
		$reg_id = $this->session->reg_id; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Select Target Clients"; 
		$pagedata['new_reg_email']  = $this->session->new_signup;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$pagedata['error_msg'] ='';
		if($this->input->post("btn_save_target") == "target_clients" )
		{
			$target_clients = $this->input->post('targeted_clients[]', TRUE);
			$target_clients_csv = array('target_clients' => implode(', ',  $target_clients ) ); 
			$this->load->model("Userdetails");  
			$this->Userdetails->update($target_clients_csv, $reg_id);   
			redirect('register/referral_partners', 'refresh');   
		}
		else 
		{
			$this->load->view('register/your_target_clients', $pagedata);
		}
		$this->load->view('template/footer',   $pagedata);  
	}
	
	
	public function referral_partners() 
	{
		if( !$this->session->has_userdata('reg_id') )
		{
			redirect('/', 'refresh'); 
		}
		
		$reg_id = $this->session->reg_id; 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Select Targeted Referral Partners"; 
		$pagedata['new_reg_email']  = $this->session->new_signup;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$pagedata['error_msg'] ='';
		if($this->input->post("btn_referral_partners") == "referral_partners" )
		{
			$referral_partners = $this->input->post('targeted_referral_partners[]', TRUE);
			$referral_partners_csv = array('target_referral_partners' => implode(', ',  $referral_partners ) ); 
			$this->load->model("Userdetails");   
			$this->Userdetails->update($referral_partners_csv, $reg_id);   
			redirect('register/choose_photo', 'refresh');   
		}
		else 
		{
			$this->load->view('register/my_referral_partner', $pagedata);
		}
		$this->load->view('template/footer',   $pagedata);  
	}
	
	
	public function choose_photo() 
	{
		if( !$this->session->has_userdata('reg_id') )
		{
			redirect('/', 'refresh'); 
		}
		
		$reg_id = $this->session->reg_id; 
		 
		$pagedata['base'] = $this->config->item('base_url');
		$pagedata['image'] = $this->config->item('image');
		$pagedata['css'] = $this->config->item('css');
		$pagedata['js']= $this->config->item('js');
		$pagedata['asset'] = $this->config->item('asset');
		$pagedata['title']  = "Upload Profile Photo"; 
		$pagedata['new_reg_email']  = $this->session->new_signup;
		$this->load->view('template/head',   $pagedata); 
		$this->load->view('template/header',   $pagedata); 
		$pagedata['error_msg'] ='';
		if($this->input->post("btn_update_photo") == "change_photo" )
		{
			$filename ='user_' . $reg_id ; 
			$config['upload_path']          =  $this->config->item('uploadpath');  
			$config['allowed_types']        = 'jpg|png|gif';
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
		else 
		{
			$this->load->view('register/choose_photo', $pagedata);
		}
		$this->load->view('template/footer',   $pagedata);  
	}
	
}
