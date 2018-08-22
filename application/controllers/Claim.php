<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Claim extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library(array('session', 'pagination', 'email'));
    }

    function randomPassword($length = 8)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&";
        $length = rand(10, 16);
        $password = substr(str_shuffle(sha1(rand() . time()) . $chars), 0, $length);
        return $password;
    }

    public function index()
    {
        $email = $this->input->post("email_input");
        $username_input = $this->input->post('username_input');
        $user_shortcode = $this->input->post('user_shortcode');
        $password = $this->randomPassword();
        $sender_email = "referralsmycity@gmail.com";
        $subject = "Claim profile succuss!";

        $data = array('email' => $email, 'password' => md5($password));
        $this->load->model("Members");

        $reg_id = $this->Members->claim_profile($data);
        $mailbody = "<p>Hi, ".ucwords($username_input)."</p>

        <p>You recently claim up MyCity.com profile with " . $email . " . You can login with below password.</p>
        <p><b>Your password: </b>" . $password . "</p>
        Visit MyCity website : <a href='" . $this->config->item('base_url') . "login' target='_blank'>" . $this->config->item('base_url') . "</a>
        <p>Sincerely,<br />
        Bob Friedenthal<br />
        Bob@mycity.com<br />
        310-736-5787</p>
        ";
        send_email($email, $sender_email,
//        send_email('vladdragonsun@gmail.com', $sender_email,
            "MyCity", $subject, $mailbody);
        echo "success";

    }
}
