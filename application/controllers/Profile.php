<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library(array('session', 'pagination'));
    }

    public function index()
    {
        $memberid = $this->uri->segment(2);

        if ($memberid == '') {
            redirect('/dashboard', 'refresh');
        }

        $pagedata['base'] = $this->config->item('base_url');
        $pagedata['site'] = $this->config->item('site_url');
        $pagedata['image'] = $this->config->item('image');
        $pagedata['css'] = $this->config->item('css');
        $pagedata['js'] = $this->config->item('js');
        $pagedata['asset'] = $this->config->item('asset');
        $pagedata['profile_img'] = $this->config->item('profile_img');
        $pagedata['site_path'] = $this->config->item('site_path');
        $this->load->model('Members');
        $member = $this->Members->getprofile($memberid);


        $pagedata['member'] = $member;
        $pagedata['title'] = "Profile";

        $this->load->view('template/head', $pagedata);
        $this->load->view('template/header', $pagedata);
        $this->load->view('template/common_header', $pagedata);
        $this->load->view('profile/index', $pagedata);
        $this->load->view('template/footer', $pagedata);
    }

    public function view()
    {
        $this->load->model('Members');
        $memberid = $this->uri->segment(2);
        $offset = $this->uri->segment(3);

        if (!intval($offset)) {
            $offset = 0;
        }
        if ($this->session->has_userdata('id')) {
            $uid = $this->session->id;
        } else {
            $uid = 0;
        }
        $this->session->unset_userdata('signup_partner_id');

        if ($this->input->post("btn_signup") == 'signup') {
            $this->session->set_userdata('signup_partner_id', $this->input->post("hidpid"));
            redirect('/login?p=' . $this->input->post("hidpid"), 'refresh');
        }

        if ($this->input->post("btn_join") == 'join') {
            $this->session->set_userdata('signup_partner_id', $this->input->post("hidpid"));
            redirect('/sign-up?p=' . $this->input->post("hidpid"), 'refresh');
        }
        $this->load->model("MyInviteKnowLog");
        $invite_details = null;
        if ($this->input->get("fil")) {
            $encode = $this->input->get("fil");
            $invite_details = $this->MyInviteKnowLog->get_invite_log_by_hash($encode);
        }

        if (intval($memberid) > 0) {
            $pagedata['noindex'] = 'true';
        }
        $pagedata['invite_details'] = $invite_details;
        $pagedata['base'] = $this->config->item('base_url');
        $pagedata['site'] = $this->config->item('site_url');
        $pagedata['image'] = $this->config->item('image');
        $pagedata['css'] = $this->config->item('css');
        $pagedata['js'] = $this->config->item('js');
        $pagedata['profile_img'] = $this->config->item('profile_img');
        $pagedata['site_path'] = $this->config->item('site_path');
        $pagedata['asset'] = $this->config->item('asset');
        $pagedata['uid'] = $uid;

        $member = $memberdet = $this->Members->getprofile($memberid);
        $pagedata['member'] = $memberdet;

        if ($memberdet->row()->id != $uid) {
            $this->load->model('MyProfileViewLog');
            $this->MyProfileViewLog->add(array('user_id' => $memberdet->row()->id,
                'ip' => $this->input->ip_address(),
                'viewed_on' => date('Y-m-d H:i:s'), 'viewed_by' => $uid));
        }

        $this->load->model('Myuserrating');
        $ratings = $this->Myuserrating->get_rating($memberdet->row()->id);
        $pagedata['ratings'] = $ratings;

        $this->load->model('Knows');
        if ($member->num_rows() > 0) {
            $profile = $member->row();
            $pagedata['urlsegment'] = $memberid;
            $pagedata['title'] = $profile->username . " Profile";
            $connections = $this->Knows->get_myknows($profile->id, $offset, 10);
            $pagedata['keyword'] = $profile->keywords;
            $pagedata['meta_desc'] = $profile->meta;
        } else {
            $profile = null;
            $connections = null;
            $pagedata['urlsegment'] = '';
            $pagedata['title'] = "No Member Selected";
        }

        if ($this->input->post("connect_req") == 'send') {
            $connect_data = array(
                'partnerid' => $this->input->post('partnerid'),
                'useremail' => $this->input->post('useremail'),
                'user_id' => $this->session->id,
                'connect_req' => $this->input->post('connect_req'));
            $req_result = $this->Members->request_connection($connect_data);

            $pagedata['req_result'] = $req_result;
            $this->session->set_userdata("msg_error", $req_result['errmsg']);
            redirect('/profile/' . $this->uri->segment(2), 'refresh');
        }

        if ($profile != null) {
            $pagedata['title'] = $profile->username . " | " . $profile->vocations;
        } else {
            $pagedata['title'] = "Member not found";
        }

        if ($this->input->post("bcp") == "claim_profile") {
            $know_page = ($this->input->post('page') ? $this->input->post('page') : 0);
            $pc_data = array(
                'knowid' => $this->input->post('knowid'),
                'email' => $this->input->post('email'),
                'user_id' => $uid,
                'name' => $this->input->post('name')
            );

            $req_result = $this->Members->send_claim_profile_invite($pc_data);
            $this->session->set_userdata("msg_error", $req_result['errmsg']);
            //redirect('/profile/' . $memberid . "/" . $know_page , 'refresh');
        }


        $pagedata['offset'] = $offset;
        $pagedata['connections'] = $connections;
        $pager_config['base_url'] = $this->config->item('base_url') . 'profile/' . $memberid;
        $pager_config['per_page'] = 10;
        $pager_config['full_tag_open'] = "<ul class='pagination'>";
        $pager_config['full_tag_close'] = "</ul>";
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
        $pagedata['pager_config'] = $pager_config;
        $this->load->view('template/head', $pagedata);
        $this->load->view('template/header', $pagedata);
        $this->load->view('template/common_header', $pagedata);
        $this->load->view('profile/index', $pagedata);
        $this->load->view('template/footer', $pagedata);
    }

    public function invite()
    {
        if ($this->session->has_userdata('id')) {
            redirect('/dashboard', 'refresh');
        }
        $this->load->model('Members');
        $this->load->model('Knows');
        $this->load->model("UserAnswers");
        $membername = $this->uri->segment(3);
        $knowname = $this->uri->segment(4);
        $answer_rs = null;
        $this->load->model("MyInviteKnowLog");
        $msg = '';
        $partner_id = '0';
        if ($this->input->post('btn_signup') == 'signup') {
            $password = md5($this->input->post('password'));
            $hash_id = $this->input->post('hashid');

            $invknow = $invitelog = $this->MyInviteKnowLog->get_invite_log_by_hash($hash_id);

            if ($invknow->num_rows() > 0) {
                $knowrow = $invknow->row_array();
                $client_name = $knowrow['client_name'];
                $client_profession = $knowrow['client_profession'];
                $client_phone = $knowrow['client_phone'];
                $client_email = $knowrow['client_email'];
                $client_location = $knowrow['client_location'];
                $client_zip = $knowrow['client_zip'];
                $targetvocation = '';
                $partner_id = $knowrow['partner_id'];
                $answer_rs = $this->UserAnswers->get_answer($knowrow['id']);
                if ($answer_rs->num_rows() > 0) {
                    $targetvocation = $answer_rs->row()->answer;
                }
                $newmember = array(
                    'username' => $client_name,
                    'user_pass' => $password,
                    'user_phone' => ($client_phone == null || $client_phone == '' ? '' : $client_phone),
                    'user_email' => $client_email,
                    'tags' => 'Know Signup'
                );
                $memberid = $this->Members->add($newmember);
                if ($memberid > 0) {
                    $member_details = array(
                        'user_id' => $memberid,
                        'vocations' => ($client_profession == null || $client_profession == '' ? '' : $client_profession),
                        'city' => ($client_location == null || $client_location == '' ? '' : $client_location),
                        'zip' => ($client_zip == null || $client_zip == '' ? '' : $client_zip),
                        'target_clients' => $targetvocation
                    );
                    $this->load->model("Userdetails");
                    $this->Userdetails->save($member_details);
                    //update member connection
                    $this->load->model("Memberconnection");
                    $this->Memberconnection->add(
                        array(
                            'firstpartner' => $partner_id,
                            'secondpartner' => $memberid,
                            'requestdate' => date('Y-m-d H:i:s'),
                            'approvedon' => date('Y-m-d H:i:s'),
                            'status' => '1'
                        )
                    );
                    $this->MyInviteKnowLog->update_log(array('join_date' => date('Y-m-d H:i:s')), $hash_id);
                    $msg = 'Signup Complete!';

                    //login
                    $rememberme = 1;
                    $data = array(
                        'email' => $client_email,
                        'password' => $this->input->post('password'),
                        'rememberme' => $rememberme);

                    $loginprofile = $this->Members->login($data);
                    $pagedata['login'] = $loginprofile;

                    if ($loginprofile['id'] != 0) {
                        $this->session->set_userdata($loginprofile);
                        $cookie = array(
                            'name' => '_mcu',
                            'value' => json_encode($loginprofile),
                            'expire' => time() + 86500,
                            'path' => '/'
                        );
                        $this->input->set_cookie($cookie);
                        $cookie = array(
                            'name' => '_mcu',
                            'value' => json_encode($loginprofile),
                            'expire' => time() + 86500,
                            'path' => '/admin'
                        );
                        $this->input->set_cookie($cookie);
                        $log_err = '';
                        redirect('/dashboard', 'refresh');
                    }
                    /* login ends */
                } else {
                    $msg = "Your account already exists. <br/>Please <a href='https://mycity.com/login'>login</a> instead!";
                }
            }
        }

        $pagedata['msg'] = $msg;
        $invitelog = $this->MyInviteKnowLog->get_invite_log(array('know' => $knowname, 'partner' => $membername));
        if ($invitelog->num_rows() > 0) {
            $log = $invitelog->row();
            $answer_rs = $this->UserAnswers->get_answer($log->know_id);
        }

        $pagedata['answer_rs'] = $answer_rs;
        $pagedata['invitelog'] = $invitelog;


        $pagedata['base'] = $this->config->item('base_url');
        $pagedata['site'] = $this->config->item('site_url');
        $pagedata['image'] = $this->config->item('image');
        $pagedata['css'] = $this->config->item('css');
        $pagedata['js'] = $this->config->item('js');
        $pagedata['profile_img'] = $this->config->item('profile_img');
        $pagedata['site_path'] = $this->config->item('site_path');
        $pagedata['asset'] = $this->config->item('asset');
        $pagedata['title'] = "Join mycity.com";
        $member = $memberdet = $this->Members->getprofile($membername);
        $pagedata['member'] = $memberdet;

        $this->load->view('template/head', $pagedata);
        $this->load->view('template/header', $pagedata);
        $this->load->view('template/common_header', $pagedata);
        $this->load->view('profile/invite', $pagedata);
        $this->load->view('template/footer', $pagedata);
    }

//    claim -> claim1
    public function claim1()
    {
        if ($this->session->has_userdata('id')) {
            redirect('/dashboard', 'refresh');
        }
        $this->load->model('Members');
        $this->load->model('Knows');
        $this->load->model("UserAnswers");
        $knowname = $this->uri->segment(3);
        $answer_rs = null;
        $this->load->model("MyInviteKnowLog");
        $msg = '';
        $partner_id = '0';

        $pagedata['msg'] = $msg;
        $invitelog = $this->MyInviteKnowLog->get_invite_log_by_know($knowname);


        if ($invitelog->num_rows() > 0) {
            $log = $invitelog->row();
            $answer_rs = $this->UserAnswers->get_answer($log->id);
        }

        $pagedata['answer_rs'] = $answer_rs;
        $pagedata['invitelog'] = $invitelog;

        $pagedata['base'] = $this->config->item('base_url');
        $pagedata['site'] = $this->config->item('site_url');
        $pagedata['image'] = $this->config->item('image');
        $pagedata['css'] = $this->config->item('css');
        $pagedata['js'] = $this->config->item('js');
        $pagedata['profile_img'] = $this->config->item('profile_img');
        $pagedata['site_path'] = $this->config->item('site_path');
        $pagedata['asset'] = $this->config->item('asset');
        $pagedata['title'] = "Claim your mycity.com profile";
        $member = $memberdet = $this->Members->getprofile($membername);
        $pagedata['member'] = $memberdet;

        $this->load->view('template/head', $pagedata);
        $this->load->view('template/header', $pagedata);
        $this->load->view('template/common_header', $pagedata);
        $this->load->view('profile/claim', $pagedata);
        $this->load->view('template/footer', $pagedata);
    }

}
