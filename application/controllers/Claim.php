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
        $first_name = $this->input->post("first_name");
        $last_name = $this->input->post('last_name');
        $email = $this->input->post('email');
        $password = $this->randomPassword();
        $sender_email = "referralsmycity@gmail.com";
        $subject = "Account created!";


        $name = $first_name . " " . $last_name;
        $data = array('username' => $name, 'user_email' => $email, 'user_pass' => md5($password));
        $this->load->model("Members");

        if ($this->Members->check_duplicate($email)) {
            echo 'Email you provided has an account already.';
            return;
        } else {
            $reg_id = $this->Members->add($data);
            $mailbody = "<p>Hi</p>

            <p>You recently signed up in MyCity.com using the email - " . $email . " . You can login with below password.</p>
            <p><b>Your password: </b>" . $password . "</p>
            Visit MyCity : <a href='" . $this->config->item('base_url') . "login' target='_blank'>" . $this->config->item('base_url') . "</a>
            <p>Sincerely,<br />
            Bob Friedenthal<br />
            Bob@mycity.com<br />
            310-736-5787</p>
            ";
            send_email($email, $sender_email,
                "MyCity", $subject, $mailbody);
            echo "success";
        }
    }
}
